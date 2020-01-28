<?php

namespace App\Controller;

use App\Entity\Entite;
use App\Form\EntiteType;
use Doctrine\ORM\EntityManagerInterface as Manager;
use FOS\RestBundle\Normalizer\ArrayNormalizerInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Utils\Utils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/api/entite")
 */
class EntiteController extends AbstractController
{
    /**
     * @Rest\Get(path="/", name="entite_index")
     * @Rest\View(StatusCode = 200)
     * @IsGranted("ROLE_Entite_INDEX")
     */
    public function index(): array
    {
        $entites = $this->getDoctrine()
            ->getRepository(Entite::class)
            ->findAll();
<<<<<<< HEAD
      

        return count($entites)?$entites:[];
=======
        return count($entites) ? $entites : [];
>>>>>>> 3386ab1528b4c3d73e0d2c23990833dd916e4dad
    }

    /**
     * @Rest\Post(Path="/create", name="entite_new")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Entite_CREATE")
     */
    public function create(Request $request, Manager $manager): Entite
    {
        $entite = new Entite();
        $form = $this->createForm(EntiteType::class, $entite);
        $form->submit(Utils::serializeRequestContent($request));
        $this->checkCodeAndNom($entite, $manager);
        $manager->persist($entite);
        $manager->flush();

        return $entite;
    }

    /**
     * @Rest\Get(path="/{id}", name="entite_show",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Entite_SHOW")
     */
    public function show(Entite $entite)
    {
        return $entite;
    }

    /**
     * @Rest\Put(path="/{id}/edit", name="entite_edit",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Entite_EDIT")
     */
    public function edit(Request $request, Entite $entite, Manager $manager): Entite
    {
        $form = $this->createForm(EntiteType::class, $entite);
        $form->submit(Utils::serializeRequestContent($request));
        $this->checkEditCodeAndNom($entite, $manager);
        $this->getDoctrine()->getManager()->flush();
        return $entite;
    }

    /**
     * @Rest\Put(path="/{id}/clone", name="entite_clone",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Entite_CLONE")
     */
    public function cloner(Request $request, Entite $entite, Manager $manager): Entite
    {
        $em = $this->getDoctrine()->getManager();
        $entiteNew = new Entite();
        $form = $this->createForm(EntiteType::class, $entiteNew);
        $form->submit(Utils::serializeRequestContent($request));
        $this->checkCodeAndNom($entiteNew, $manager);
        $em->persist($entiteNew);

        $em->flush();

        return $entiteNew;
    }

    /**
     * @Rest\Delete("/{id}", name="entite_delete",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Entite_DELETE")
     */
    public function delete(Entite $entite): Entite
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($entite);
        $entityManager->flush();

        return $entite;
    }

    /**
     * @Rest\Post("/delete-selection/", name="entite_selection_delete")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Entite_DELETE")
     */
    public function deleteMultiple(Request $request): array
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entites = Utils::getObjectFromRequest($request);
        if (!count($entites)) {
            throw $this->createNotFoundException("Selectionner au minimum un élément à supprimer.");
        }
        foreach ($entites as $entite) {
            $entite = $entityManager->getRepository(Entite::class)->find($entite->id);
            $entityManager->remove($entite);
        }
        $entityManager->flush();

        return $entites;
    }

    /**
     * @Rest\Get(path="/{id}/sous-entites", name="entite_sous_entites",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Entite_SHOW")
     */
    public function findSousEntites(Entite $entite, Manager $manager)
    {
        $sousEntites = $manager->createQuery('select e from App\Entity\Entite e where e.entiteParent = :parent')
            ->setParameter('parent', $entite->getId())->getResult();

        return $sousEntites;
    }

    ///////////////////////////////// Tests /////////////////////////////////

    public function checkEditCodeAndNom(Entite $entite, Manager $manager)
    {
        // check if nom already exist
        $searchedEntiteByCode = $manager->createQuery("select e from App\Entity\Entite e where e != :entite and e.code = :code")->setParameter('entite', $entite)->setParameter('code', $entite->getCode())->getResult();
        if (count($searchedEntiteByCode)) {
            throw $this->createAccessDeniedException("Une entité avec le même code existe déjà, merci de changer de code...");
        }

        // check if nom already exist
        $searchedEntiteByNom = $manager->createQuery("select e from App\Entity\Entite e where e != :entite and e.nom = :name")->setParameter('entite', $entite)->setParameter('name', $entite->getNom())->getResult();
        if (count($searchedEntiteByNom)) {
            throw $this->createAccessDeniedException("Une entité avec le même nom existe déjà, merci de changer de nom...");
        }
    }

    public function checkCodeAndNom(Entite $entite, Manager $manager)
    {
        // check if code already exit
        $searchedEntiteByCode = $manager->getRepository(Entite::class)->findByCode($entite->getCode());
        if (count($searchedEntiteByCode)) {
            throw $this->createAccessDeniedException("Une entité avec le même code existe déjà, merci de changer de code...");
        }
        // check if nom already exit
        $searchedEntiteByNom = $manager->getRepository(Entite::class)->findByNom($entite->getNom());
        if (count($searchedEntiteByNom)) {
            throw $this->createAccessDeniedException("Une entité avec le même nom existe déjà, merci de changer de nom...");
        }
    }
}
