<?php

namespace App\Controller;

use App\Entity\ExerciceSourceFinancement;
use App\Form\ExerciceSourceFinancementType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Utils\Utils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/api/exerciceSourceFinancement")
 */
class ExerciceSourceFinancementController extends AbstractController
{
    /**
     * @Rest\Get(path="/", name="exercice_source_financement_index")
     * @Rest\View(StatusCode = 200)
     * @IsGranted("ROLE_ExerciceSourceFinancement_INDEX")
     */
    public function index(): array
    {
        $exerciceSourceFinancements = $this->getDoctrine()
            ->getRepository(ExerciceSourceFinancement::class)
            ->findAll();

        return count($exerciceSourceFinancements)?$exerciceSourceFinancements:[];
    }

    /**
     * @Rest\Post(Path="/create", name="exercice_source_financement_new")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_ExerciceSourceFinancement_CREATE")
     */
    public function create(Request $request): ExerciceSourceFinancement    {
        $exerciceSourceFinancement = new ExerciceSourceFinancement();
        $form = $this->createForm(ExerciceSourceFinancementType::class, $exerciceSourceFinancement);
        $form->submit(Utils::serializeRequestContent($request));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($exerciceSourceFinancement);
        $entityManager->flush();

        return $exerciceSourceFinancement;
    }

    /**
     * @Rest\Get(path="/{id}", name="exercice_source_financement_show",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_ExerciceSourceFinancement_SHOW")
     */
    public function show(ExerciceSourceFinancement $exerciceSourceFinancement): ExerciceSourceFinancement    {
        return $exerciceSourceFinancement;
    }

    
    /**
     * @Rest\Put(path="/{id}/edit", name="exercice_source_financement_edit",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_ExerciceSourceFinancement_EDIT")
     */
    public function edit(Request $request, ExerciceSourceFinancement $exerciceSourceFinancement): ExerciceSourceFinancement    {
        $form = $this->createForm(ExerciceSourceFinancementType::class, $exerciceSourceFinancement);
        $form->submit(Utils::serializeRequestContent($request));

        $this->getDoctrine()->getManager()->flush();

        return $exerciceSourceFinancement;
    }
    
    /**
     * @Rest\Put(path="/{id}/clone", name="exercice_source_financement_clone",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_ExerciceSourceFinancement_CLONE")
     */
    public function cloner(Request $request, ExerciceSourceFinancement $exerciceSourceFinancement):  ExerciceSourceFinancement {
        $em=$this->getDoctrine()->getManager();
        $exerciceSourceFinancementNew=new ExerciceSourceFinancement();
        $form = $this->createForm(ExerciceSourceFinancementType::class, $exerciceSourceFinancementNew);
        $form->submit(Utils::serializeRequestContent($request));
        $em->persist($exerciceSourceFinancementNew);

        $em->flush();

        return $exerciceSourceFinancementNew;
    }

    /**
     * @Rest\Delete("/{id}", name="exercice_source_financement_delete",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_ExerciceSourceFinancement_DELETE")
     */
    public function delete(ExerciceSourceFinancement $exerciceSourceFinancement): ExerciceSourceFinancement    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($exerciceSourceFinancement);
        $entityManager->flush();

        return $exerciceSourceFinancement;
    }
    
    /**
     * @Rest\Post("/delete-selection/", name="exercice_source_financement_selection_delete")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_ExerciceSourceFinancement_DELETE")
     */
    public function deleteMultiple(Request $request): array {
        $entityManager = $this->getDoctrine()->getManager();
        $exerciceSourceFinancements = Utils::getObjectFromRequest($request);
        if (!count($exerciceSourceFinancements)) {
            throw $this->createNotFoundException("Selectionner au minimum un élément à supprimer.");
        }
        foreach ($exerciceSourceFinancements as $exerciceSourceFinancement) {
            $exerciceSourceFinancement = $entityManager->getRepository(ExerciceSourceFinancement::class)->find($exerciceSourceFinancement->id);
            $entityManager->remove($exerciceSourceFinancement);
        }
        $entityManager->flush();

        return $exerciceSourceFinancements;
    }
}
