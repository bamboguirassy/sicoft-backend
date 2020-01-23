<?php

namespace App\Controller;

use App\Entity\TypeEntite;
use App\Form\TypeEntiteType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Utils\Utils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/api/typeEntite")
 */
class TypeEntiteController extends AbstractController
{
    /**
     * @Rest\Get(path="/", name="type_entite_index")
     * @Rest\View(StatusCode = 200)
     * @IsGranted("ROLE_TypeEntite_INDEX")
     */
    public function index(): array
    {
        $typeEntites = $this->getDoctrine()
            ->getRepository(TypeEntite::class)
            ->findAll();

        return count($typeEntites)?$typeEntites:[];
    }

    /**
     * @Rest\Post(Path="/create", name="type_entite_new")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_TypeEntite_CREATE")
     */
    public function create(Request $request): TypeEntite    {
        $typeEntite = new TypeEntite();
        $form = $this->createForm(TypeEntiteType::class, $typeEntite);
        $form->submit(Utils::serializeRequestContent($request));

        $em = $this->getDoctrine()->getManager();
        $searchedTypeEntiteByCode = $em->getRepository(TypeEntite::class)->findOneByCode($typeEntite->getCode());

        if($searchedTypeEntiteByCode) {
            throw $this->createAccessDeniedException('Un Type Entite avec le même code existe déja.');
        }

        $searchedTypeEntiteByLabel = $em->getRepository(TypeEntite::class)->findOneByLibelle($typeEntite->getLibelle());
        if($searchedTypeEntiteByLabel) {
            throw $this->createAccessDeniedException('Un Type Entite avec le même libelle existe déja.');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($typeEntite);
        $entityManager->flush();

        return $typeEntite;
    }

    /**
     * @Rest\Get(path="/{id}", name="type_entite_show",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_TypeEntite_SHOW")
     */
    public function show(TypeEntite $typeEntite): TypeEntite    {
        return $typeEntite;
    }

    
    /**
     * @Rest\Put(path="/{id}/edit", name="type_entite_edit",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_TypeEntite_EDIT")
     */
    public function edit(Request $request, TypeEntite $typeEntite): TypeEntite    {
        $form = $this->createForm(TypeEntiteType::class, $typeEntite);
        $form->submit(Utils::serializeRequestContent($request));

        $this->getDoctrine()->getManager()->flush();

        return $typeEntite;
    }
    
    /**
     * @Rest\Put(path="/{id}/clone", name="type_entite_clone",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_TypeEntite_CLONE")
     */
    public function cloner(Request $request, TypeEntite $typeEntite):  TypeEntite {
        $em=$this->getDoctrine()->getManager();
        $typeEntiteNew=new TypeEntite();
        $form = $this->createForm(TypeEntiteType::class, $typeEntiteNew);
        $form->submit(Utils::serializeRequestContent($request));

        $searchedTypeEntiteByCode = $em->getRepository(TypeEntite::class)
            ->findOneByCode($typeEntiteNew->getCode());
        if($searchedTypeEntiteByCode) {
            throw $this->createAccessDeniedException('Un Type Entite avec le même code existe déja.');
        }

        $searchedTypeEntiteByLabel = $em->getRepository(TypeEntite::class)
            ->findOneByLibelle($typeEntiteNew->getLibelle());
        if($searchedTypeEntiteByLabel) {
            throw $this->createAccessDeniedException('Un Type Entite avec le même libelle existe déja.');
        }

        $em->persist($typeEntiteNew);
        $em->flush();

        return $typeEntiteNew;
    }

    /**
     * @Rest\Delete("/{id}", name="type_entite_delete",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_TypeEntite_DELETE")
     */
    public function delete(TypeEntite $typeEntite): TypeEntite    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($typeEntite);
        $entityManager->flush();

        return $typeEntite;
    }
    
    /**
     * @Rest\Post("/delete-selection/", name="type_entite_selection_delete")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_TypeEntite_DELETE")
     */
    public function deleteMultiple(Request $request): array {
        $entityManager = $this->getDoctrine()->getManager();
        $typeEntites = Utils::getObjectFromRequest($request);
        if (!count($typeEntites)) {
            throw $this->createNotFoundException("Selectionner au minimum un élément à supprimer.");
        }
        foreach ($typeEntites as $typeEntite) {
            $typeEntite = $entityManager->getRepository(TypeEntite::class)->find($typeEntite->id);
            $entityManager->remove($typeEntite);
        }
        $entityManager->flush();

        return $typeEntites;
    }
}
