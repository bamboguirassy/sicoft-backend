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

/**
 * @Route("/api/typePassation")
 */
class TypePassationController extends AbstractController
{
    /**
     * @Rest\Get(path="/", name="type_passation_index")
     * @Rest\View(StatusCode = 200)
     * @IsGranted("ROLE_TypePassation_INDEX")
     */
    public function index(): array
    {
        $typePassations = $this->getDoctrine()
            ->getRepository(TypePassation::class)
            ->findAll();

        return count($typePassations)?$typePassations:[];
    }

    /**
     * @Rest\Post(Path="/create", name="type_passation_new")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_TypePassation_CREATE")
     */
    public function create(Request $request): TypePassation    {
        $typePassation = new TypePassation();
        $form = $this->createForm(TypePassationType::class, $typePassation);
        $form->submit(Utils::serializeRequestContent($request));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($typePassation);
        $entityManager->flush();

        return $typePassation;
    }

    /**
     * @Rest\Get(path="/{id}", name="type_passation_show",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_TypePassation_SHOW")
     */
    public function show(TypePassation $typePassation): TypePassation    {
        return $typePassation;
    }

    
    /**
     * @Rest\Put(path="/{id}/edit", name="type_passation_edit",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_TypePassation_EDIT")
     */
    public function edit(Request $request, TypePassation $typePassation): TypePassation    {
        $form = $this->createForm(TypePassationType::class, $typePassation);
        $form->submit(Utils::serializeRequestContent($request));

        $this->getDoctrine()->getManager()->flush();

        return $typePassation;
    }
    
    /**
     * @Rest\Put(path="/{id}/clone", name="type_passation_clone",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_TypePassation_CLONE")
     */
    public function cloner(Request $request, TypePassation $typePassation):  TypePassation {
        $em=$this->getDoctrine()->getManager();
        $typePassationNew=new TypePassation();
        $form = $this->createForm(TypePassationType::class, $typePassationNew);
        $form->submit(Utils::serializeRequestContent($request));
        $em->persist($typePassationNew);

        $em->flush();

        return $typePassationNew;
    }

    /**
     * @Rest\Delete("/{id}", name="type_passation_delete",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_TypePassation_DELETE")
     */
    public function delete(TypePassation $typePassation): TypePassation    {
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
}
