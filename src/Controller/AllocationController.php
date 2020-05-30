<?php

namespace App\Controller;

use App\Entity\Allocation;
use App\Entity\Budget;
use App\Entity\ExerciceSourceFinancement;
use App\Form\AllocationType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Utils\Utils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/api/allocation")
 */
class AllocationController extends AbstractController
{
    /**
     * @Rest\Get(path="/", name="allocation_index")
     * @Rest\View(StatusCode = 200)
     * @IsGranted("ROLE_Allocation_INDEX")
     */
    public function index(): array
    {
        $allocations = $this->getDoctrine()
            ->getRepository(Allocation::class)
            ->findAll();

        return count($allocations) ? $allocations : [];
    }

    /**
     * @Rest\Get(path="/{id}/esf", name="fetch_recette_allocation", requirements={"id"="\d+", "budgetId"="\d+"})
     * @Rest\View(StatusCode = 200)
     * @IsGranted("ROLE_Allocation_INDEX")
     */
    public function findByExerciceSrcFin(Request $request, ExerciceSourceFinancement $exerciceSourceFinancement, EntityManagerInterface $entityManager)
    {
        return $entityManager->createQuery('
            SELECT a
            FROM App\Entity\Allocation a
            JOIN a.exerciceSourceFinancement esf
            JOIN esf.budget budget
            WHERE esf=:exSrcFin 
        ')->setParameter('exSrcFin', $exerciceSourceFinancement)
            ->getResult();
    }

    /**
     * @Rest\Get(path="/{id}/budget-depense", name="fetch_depense_allocation", requirements={"id"="\d+"})
     * @Rest\View(StatusCode = 200)
     * @IsGranted("ROLE_Allocation_INDEX")
     */
    public function findDepenseByBudget(Request $request, Budget $budget, EntityManagerInterface $entityManager)
    {
        $totalDepense = 0.0;
        $totalRecette = 0.0;

        try {
            $totalDepense = (float)$entityManager->createQuery('
                SELECT SUM(a.montantInitial) as totalDepense
                FROM App\Entity\Allocation a
                JOIN a.compte c
                JOIN c.compteDivisionnaire cd 
                JOIN cd.sousClasse scl
                JOIN scl.classe cl 
                WHERE cl.typeClasse IN (SELECT type FROM App\Entity\TypeClasse type WHERE type.code=2)
                AND a.budget=:budget
            ')->setParameter('budget', $budget)->getSingleScalarResult();
            $totalRecette = (float)$entityManager->createQuery('
                    SELECT SUM(a.montantInitial) - :totalDepense
                    FROM App\Entity\Allocation a
                    JOIN a.exerciceSourceFinancement esf
                    JOIN a.compte c
                    JOIN c.compteDivisionnaire cd 
                    JOIN cd.sousClasse scl
                    JOIN scl.classe cl 
                    WHERE cl.typeClasse IN (SELECT type FROM App\Entity\TypeClasse type WHERE type.code=1)
                    AND esf.budget=:budget
                ')->setParameter('budget', $budget)
                ->setParameter('totalDepense', $totalDepense)
                ->getSingleScalarResult();
        } catch (NoResultException $e) {
        } catch (NonUniqueResultException $e) {
        }

        $allocationsDepenses = $entityManager->createQuery('
            SELECT a
            FROM App\Entity\Allocation a
            WHERE a.budget=:budget
        ')->setParameter('budget', $budget)
            ->getResult();

        return [
            'totalRecette' => $totalRecette,
            'totalDepense' => $totalDepense,
            'allocationDepenses' => $allocationsDepenses
        ];
    }

    /**
     * @Rest\Get(path="/{id}/{divId}/budget-cd", name="fetch_allocation", requirements={"id"="\d+", "divId"="\d+"})
     * @Rest\View(StatusCode = 200)
     * @IsGranted("ROLE_Allocation_INDEX")
     * @Rest\QueryParam(
     *  name="compteType",
     *  requirements="recette|depense",
     *  default="recette"
     * )
     */
    public function findByBudgetAndCompteDivisionnaire(Request $request, Budget $budget, $divId, EntityManagerInterface $entityManager, $compteType)
    {
        $dqlQuery = $compteType === 'recette' ?
            'SELECT a
            FROM App\Entity\Allocation a
            JOIN a.compte c
            JOIN c.compteDivisionnaire cd
            JOIN a.exerciceSourceFinancement esf
            WHERE cd.id=:compteDiv AND esf.budget=:budget'
            :
            'SELECT a
            FROM App\Entity\Allocation a
            JOIN a.compte c
            JOIN c.compteDivisionnaire cd
            WHERE cd.id=:compteDiv AND a.budget=:budget';
        return $entityManager->createQuery($dqlQuery)->setParameter('compteDiv', $divId)
            ->setParameter('budget', $budget)
            ->getResult();
    }

    /**
     * @Rest\Get(path="/{id}/montant-recette-depense", name="fetch_recette_allocations", requirements={"id"="\d+"})
     * @Rest\View(StatusCode = 200)
     * @IsGranted("ROLE_Allocation_INDEX")
     */
    public function calculateTotalAllocationsRecetteAndDepenseByBudget(Request $request, Budget $budget, EntityManagerInterface $entityManager)
    {
        try {

            $totalDepense = $entityManager->createQuery('
                SELECT SUM(a.montantInitial) as totalDepense
                FROM App\Entity\Allocation a
                JOIN a.compte c
                JOIN c.compteDivisionnaire cd 
                JOIN cd.sousClasse scl
                JOIN scl.classe cl 
                WHERE cl.typeClasse IN (SELECT type FROM App\Entity\TypeClasse type WHERE type.code=2)
                AND a.budget=:budget
            ')->setParameter('budget', $budget)->getSingleScalarResult();

            $totalDepense = (float)$totalDepense;

            $totalRecette = $entityManager->createQuery('
                SELECT SUM(a.montantInitial) - :totalDepense
                FROM App\Entity\Allocation a
                JOIN a.exerciceSourceFinancement esf
                JOIN a.compte c
                JOIN c.compteDivisionnaire cd 
                JOIN cd.sousClasse scl
                JOIN scl.classe cl 
                WHERE cl.typeClasse IN (SELECT type FROM App\Entity\TypeClasse type WHERE type.code=1)
                AND esf.budget=:budget
            ')->setParameter('budget', $budget)
                ->setParameter('totalDepense', $totalDepense)
                ->getSingleScalarResult();

            return [
                'totalRecette' => (float)$totalRecette,
                'totalDepense' => $totalDepense
            ];

        } catch (NoResultException $e) {
            return ['message' => $e->getMessage()];
        } catch (NonUniqueResultException $e) {
            return ['message' => $e->getMessage()];
        }
    }

    /**
     * @Rest\Post(Path="/create", name="allocation_new")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Allocation_CREATE")
     */
    public function create(Request $request): Allocation
    {
        $allocation = new Allocation();
        $form = $this->createForm(AllocationType::class, $allocation);
        $form->submit(Utils::serializeRequestContent($request));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($allocation);
        $entityManager->flush();

        return $allocation;
    }

    /**
     * @Rest\Post(Path="/create-multiple-recette", name="allocation_multiple_new_recette")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Allocation_CREATE")
     */
    public function createMultipleRecette(Request $request, EntityManagerInterface $entityManager)
    {
        $deserializedAllocations = Utils::serializeRequestContent($request);
        $createdAllocations = [];
        $newAmount = 0;
        foreach ($deserializedAllocations as $deserializedAllocation) {
            $allocation = new Allocation();
            $form = $this->createForm(AllocationType::class, $allocation);
            $form->submit($deserializedAllocation);

            $entityManager->persist($allocation);
            $newAmount += $allocation->getMontantInitial();
            $createdAllocations[] = $allocation;
        }
        /** @var ExerciceSourceFinancement $esf */
        $esf = $entityManager->getRepository(ExerciceSourceFinancement::class)
            ->find($deserializedAllocations[0]['exerciceSourceFinancement']);
        $allocatedAmount = $esf->getMontantInitial() - $esf->getMontantRestant();
        $esf->setMontantRestant($esf->getMontantInitial() - ($newAmount + $allocatedAmount));
        $entityManager->flush();
        return $createdAllocations;
    }

    /**
     * @Rest\Post(Path="/create-multiple-depense", name="allocation_multiple_depense")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Allocation_CREATE")
     */
    public function createMultipleDepense(Request $request, EntityManagerInterface $entityManager)
    {
        $deserializedAllocations = Utils::serializeRequestContent($request);
        $createdAllocations = [];
        foreach ($deserializedAllocations as $deserializedAllocation) {
            $allocation = new Allocation();
            $form = $this->createForm(AllocationType::class, $allocation);
            $form->submit($deserializedAllocation);

            $entityManager->persist($allocation);
            $createdAllocations[] = $allocation;
        }
        $entityManager->flush();
        return $createdAllocations;
    }

    /**
     * @Rest\Get(path="/{id}", name="allocation_show",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Allocation_SHOW")
     */
    public function show(Allocation $allocation): Allocation
    {
        return $allocation;
    }


    /**
     * @Rest\Put(path="/{id}/edit", name="allocation_edit",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Allocation_EDIT")
     */
    public function edit(Request $request, Allocation $allocation): Allocation
    {
        $form = $this->createForm(AllocationType::class, $allocation);
        $form->submit(Utils::serializeRequestContent($request));

        $this->getDoctrine()->getManager()->flush();

        return $allocation;
    }

    /**
     * @Rest\Put(path="/edit-multiple-recette", name="allocation_edit_multiple_recette")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Allocation_EDIT")
     */
    public function editMultipleRecette(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        //Fonction mettant à jour aussi le montant du source financement
        $deserializedAllocations = Utils::serializeRequestContent($request);
        $updatedAllocation = [];
        $newAmount = 0;
        foreach ($deserializedAllocations as $allocation) {
            /** @var Allocation $persistedAllocation */
            $persistedAllocation = $entityManager
                ->getRepository(Allocation::class)
                ->find($allocation['id']);
            $form = $this->createForm(AllocationType::class, $persistedAllocation);
            $form->submit($allocation);
            $newAmount += $persistedAllocation->getMontantInitial();
            $updatedAllocation[] = $persistedAllocation;
        }
        /** @var ExerciceSourceFinancement $esf */
        $esf = $entityManager->getRepository(ExerciceSourceFinancement::class)
            ->find($deserializedAllocations[0]['exerciceSourceFinancement']['id']);
        $allocatedAmount = $esf->getMontantInitial() - $esf->getMontantRestant();
        $esf->setMontantRestant($esf->getMontantRestant() + $allocatedAmount - $newAmount);
        $entityManager->flush();
        return $updatedAllocation;
    }

    /**
     * @Rest\Put(path="/edit-multiple-depense", name="allocation_edit_multiple_depense")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Allocation_EDIT")
     */
    public function editMultipleDepense(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        $deserializedAllocations = Utils::serializeRequestContent($request);
        $updatedAllocation = [];
        foreach ($deserializedAllocations as $allocation) {
            /** @var Allocation $persistedAllocation */
            $persistedAllocation = $entityManager
                ->getRepository(Allocation::class)
                ->find($allocation['id']);
            $form = $this->createForm(AllocationType::class, $persistedAllocation);
            $form->submit($allocation);
            $updatedAllocation[] = $persistedAllocation;
        }
        $entityManager->flush();
        return $updatedAllocation;
    }

    /**
     * @Rest\Put(path="/{id}/clone", name="allocation_clone",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Allocation_CLONE")
     */
    public function cloner(Request $request, Allocation $allocation): Allocation
    {
        $em = $this->getDoctrine()->getManager();
        $allocationNew = new Allocation();
        $form = $this->createForm(AllocationType::class, $allocationNew);
        $form->submit(Utils::serializeRequestContent($request));
        $em->persist($allocationNew);

        $em->flush();

        return $allocationNew;
    }

    /**
     * @Rest\Delete("/{id}", name="allocation_delete",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Allocation_DELETE")
     */
    public function delete(Allocation $allocation): Allocation
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($allocation);
        $entityManager->flush();

        return $allocation;
    }

    /**
     * @Rest\Post("/delete-selection/", name="allocation_selection_delete")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Allocation_DELETE")
     */
    public function deleteMultiple(Request $request): array
    {
        $entityManager = $this->getDoctrine()->getManager();
        $allocations = Utils::getObjectFromRequest($request);
        if (!count($allocations)) {
            throw $this->createNotFoundException("Selectionner au minimum un élément à supprimer.");
        }
        foreach ($allocations as $allocation) {
            $allocation = $entityManager->getRepository(Allocation::class)->find($allocation->id);
            $entityManager->remove($allocation);
        }
        $entityManager->flush();

        return $allocations;
    }
}
