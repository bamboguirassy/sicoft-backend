<?php

namespace App\Controller;

use App\Entity\TypePassation;
use App\Form\TypePassationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Utils\Utils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @Route("/api/typePassation")
 */
class TypePassationController extends AbstractController {

    /**
     * @Rest\Get(path="/", name="type_passation_index")
     * @Rest\View(StatusCode = 200)
     * @IsGranted("ROLE_TypePassation_INDEX")
     */
    public function index(): array {
        $typePassations = $this->getDoctrine()
                ->getRepository(TypePassation::class)
                ->findAll();

        return count($typePassations) ? $typePassations : [];
    }

    /**
     * @Rest\Post(Path="/create", name="type_passation_new")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_TypePassation_CREATE")
     */
    public function create(Request $request, EntityManagerInterface $em): TypePassation {
        $typePassation = new TypePassation();
        $form = $this->createForm(TypePassationType::class, $typePassation);
        $form->submit(Utils::serializeRequestContent($request));
        
        // check if code and libelle already exist
        $this->checkCodeAndLibelle($typePassation, $em);
        
        $em->persist($typePassation);
        $em->flush();

        return $typePassation;
    }

    /**
     * @Rest\Get(path="/{id}", name="type_passation_show",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_TypePassation_SHOW")
     */
    public function show(TypePassation $typePassation): TypePassation {
        return $typePassation;
    }

    /**
     * @Rest\Put(path="/{id}/edit", name="type_passation_edit",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_TypePassation_EDIT")
     */
    public function edit(Request $request, TypePassation $typePassation, EntityManagerInterface $em): TypePassation {
        
        $form = $this->createForm(TypePassationType::class, $typePassation);
        $form->submit(Utils::serializeRequestContent($request));
        
        // check if code and libelle already exist
        $this->checkEditTypePassatonCodeAndLibelle($typePassation, $em);
        
        $em->flush();

        return $typePassation;
    }

    /**
     * @Rest\Put(path="/{id}/clone", name="type_passation_clone",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_TypePassation_CLONE")
     */
    public function cloner(Request $request, TypePassation $typePassation, EntityManagerInterface $em): TypePassation {
        $typePassationNew = new TypePassation();
        $form = $this->createForm(TypePassationType::class, $typePassationNew);
        $form->submit(Utils::serializeRequestContent($request));
        
        // check if code and libelle already exist
        $this->checkCodeAndLibelle($typePassationNew, $em);
        
        $em->persist($typePassationNew);
        $em->flush();

        return $typePassationNew;
    }

    /**
     * @Rest\Delete("/{id}", name="type_passation_delete",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_TypePassation_DELETE")
     */
    public function delete(TypePassation $typePassation): TypePassation {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($typePassation);
        $entityManager->flush();

        return $typePassation;
    }

    /**
     * @Rest\Post("/delete-selection/", name="type_passation_selection_delete")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_TypePassation_DELETE")
     */
    public function deleteMultiple(Request $request): array {
        $entityManager = $this->getDoctrine()->getManager();
        $typePassations = Utils::getObjectFromRequest($request);
        if (!count($typePassations)) {
            throw $this->createNotFoundException("Selectionner au minimum un élément à supprimer.");
        }
        foreach ($typePassations as $typePassation) {
            $typePassation = $entityManager->getRepository(TypePassation::class)->find($typePassation->id);
            $entityManager->remove($typePassation);
        }
        $entityManager->flush();

        return $typePassations;
    }
    
     //////////////////////////////////////// Tests /////////////////////////////////////////////
    
    public function checkEditTypePassatonCodeAndLibelle(TypePassation $type, EntityManagerInterface $em) {
        $searchedTypeByCode = $em->createQuery("select t from App\Entity\TypePassation t where t != :type and t.code = :code")->setParameter('type', $type)->setParameter('code', $type->getCode())->getResult();
        if (count($searchedTypeByCode)) {
            throw $this->createAccessDeniedException("Un Type Passation avec le même code existe déjà, merci de changer de code...");
        }
        // check if libelle already exist
       $searchedTypeByLibelle = $em->createQuery("select t from App\Entity\TypePassation t where t != :type and t.libelle = :lib")->setParameter('type', $type)->setParameter('lib', $type->getLibelle())->getResult();
        if (count($searchedTypeByLibelle)) {
            throw $this->createAccessDeniedException("Un Type Passation avec le même libellé existe déjà, merci de changer de libellé...");
        }
    }
    
    
    public function checkCodeAndLibelle(TypePassation $type, EntityManagerInterface $em) {
        $searchedTypeByCode = $em->getRepository(TypePassation::class)->findByCode($type->getCode());
        if (count($searchedTypeByCode)) {
            throw $this->createAccessDeniedException("Un Type Passation avec le même code existe déjà, merci de changer de code...");
        }
        // check if libelle already exit
        $searchedTypeByLibelle = $em->getRepository(TypePassation::class)->findByLibelle($type->getLibelle());
        if (count($searchedTypeByLibelle)) {
            throw $this->createAccessDeniedException("Un Type Passation avec le même libellé existe déjà, merci de changer de libellé...");
        }
    }

}
