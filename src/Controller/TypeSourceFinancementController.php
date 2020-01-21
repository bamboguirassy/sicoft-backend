<?php

namespace App\Controller;

use App\Entity\TypeSourceFinancement;
use App\Form\TypeSourceFinancementType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Utils\Utils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/api/typeSourceFinancement")
 */
class TypeSourceFinancementController extends AbstractController
{
    /**
     * @Rest\Get(path="/", name="type_source_financement_index")
     * @Rest\View(StatusCode = 200)
     * @IsGranted("ROLE_TypeSourceFinancement_INDEX")
     */
    public function index(): array
    {
        $typeSourceFinancements = $this->getDoctrine()
            ->getRepository(TypeSourceFinancement::class)
            ->findAll();

        return count($typeSourceFinancements)?$typeSourceFinancements:[];
    }

    /**
     * @Rest\Post(Path="/create", name="type_source_financement_new")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_TypeSourceFinancement_CREATE")
     */
    public function create(Request $request): TypeSourceFinancement    {
        $typeSourceFinancement = new TypeSourceFinancement();
        $form = $this->createForm(TypeSourceFinancementType::class, $typeSourceFinancement);
        $form->submit(Utils::serializeRequestContent($request));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($typeSourceFinancement);
        $entityManager->flush();

        return $typeSourceFinancement;
    }

    /**
     * @Rest\Get(path="/{id}", name="type_source_financement_show",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_TypeSourceFinancement_SHOW")
     */
    public function show(TypeSourceFinancement $typeSourceFinancement): TypeSourceFinancement    {
        return $typeSourceFinancement;
    }

    
    /**
     * @Rest\Put(path="/{id}/edit", name="type_source_financement_edit",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_TypeSourceFinancement_EDIT")
     */
    public function edit(Request $request, TypeSourceFinancement $typeSourceFinancement): TypeSourceFinancement    {
        $form = $this->createForm(TypeSourceFinancementType::class, $typeSourceFinancement);
        $form->submit(Utils::serializeRequestContent($request));

        $this->getDoctrine()->getManager()->flush();

        return $typeSourceFinancement;
    }
    
    /**
     * @Rest\Put(path="/{id}/clone", name="type_source_financement_clone",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_TypeSourceFinancement_CLONE")
     */
    public function cloner(Request $request, TypeSourceFinancement $typeSourceFinancement):  TypeSourceFinancement {
        $em=$this->getDoctrine()->getManager();
        $typeSourceFinancementNew=new TypeSourceFinancement();
        $form = $this->createForm(TypeSourceFinancementType::class, $typeSourceFinancementNew);
        $form->submit(Utils::serializeRequestContent($request));
        $em->persist($typeSourceFinancementNew);

        $em->flush();

        return $typeSourceFinancementNew;
    }

    /**
     * @Rest\Delete("/{id}", name="type_source_financement_delete",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_TypeSourceFinancement_DELETE")
     */
    public function delete(TypeSourceFinancement $typeSourceFinancement): TypeSourceFinancement    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($typeSourceFinancement);
        $entityManager->flush();

        return $typeSourceFinancement;
    }
    
    /**
     * @Rest\Post("/delete-selection/", name="type_source_financement_selection_delete")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_TypeSourceFinancement_DELETE")
     */
    public function deleteMultiple(Request $request): array {
        $entityManager = $this->getDoctrine()->getManager();
        $typeSourceFinancements = Utils::getObjectFromRequest($request);
        if (!count($typeSourceFinancements)) {
            throw $this->createNotFoundException("Selectionner au minimum un élément à supprimer.");
        }
        foreach ($typeSourceFinancements as $typeSourceFinancement) {
            $typeSourceFinancement = $entityManager->getRepository(TypeSourceFinancement::class)->find($typeSourceFinancement->id);
            $entityManager->remove($typeSourceFinancement);
        }
        $entityManager->flush();

        return $typeSourceFinancements;
    }
}
