<?php

namespace App\Controller;

use App\Entity\Allocation;
use App\Form\AllocationType;
use Doctrine\ORM\EntityManagerInterface;
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

        return count($allocations)?$allocations:[];
    }

    /**
     * @Rest\Post(Path="/create", name="allocation_new")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Allocation_CREATE")
     */
    public function create(Request $request): Allocation    {
        $allocation = new Allocation();
        $form = $this->createForm(AllocationType::class, $allocation);
        $form->submit(Utils::serializeRequestContent($request));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($allocation);
        $entityManager->flush();

        return $allocation;
    }

    /**
     * @Rest\Post(Path="/create-multiple", name="allocation_multiple_new")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Allocation_CREATE")
     */
    public function createMultiple(Request $request, EntityManagerInterface $entityManager) {
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
    public function show(Allocation $allocation): Allocation    {
        return $allocation;
    }

    
    /**
     * @Rest\Put(path="/{id}/edit", name="allocation_edit",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Allocation_EDIT")
     */
    public function edit(Request $request, Allocation $allocation): Allocation    {
        $form = $this->createForm(AllocationType::class, $allocation);
        $form->submit(Utils::serializeRequestContent($request));

        $this->getDoctrine()->getManager()->flush();

        return $allocation;
    }
    
    /**
     * @Rest\Put(path="/{id}/clone", name="allocation_clone",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Allocation_CLONE")
     */
    public function cloner(Request $request, Allocation $allocation):  Allocation {
        $em=$this->getDoctrine()->getManager();
        $allocationNew=new Allocation();
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
    public function delete(Allocation $allocation): Allocation    {
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
    public function deleteMultiple(Request $request): array {
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
