<?php

namespace App\Controller;

use App\Entity\SourceFinancement;
use App\Form\SourceFinancementType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Utils\Utils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/api/sourceFinancement")
 */
class SourceFinancementController extends AbstractController
{
    /**
     * @Rest\Get(path="/", name="source_financement_index")
     * @Rest\View(StatusCode = 200)
     * @IsGranted("ROLE_SourceFinancement_INDEX")
     */
    public function index(): array
    {
        $sourceFinancements = $this->getDoctrine()
            ->getRepository(SourceFinancement::class)
            ->findAll();

        return count($sourceFinancements)?$sourceFinancements:[];
    }

    /**
     * @Rest\Post(Path="/create", name="source_financement_new")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_SourceFinancement_CREATE")
     */
    public function create(Request $request): SourceFinancement    {
        $sourceFinancement = new SourceFinancement();
        $form = $this->createForm(SourceFinancementType::class, $sourceFinancement);
        $form->submit(Utils::serializeRequestContent($request));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($sourceFinancement);
        $entityManager->flush();

        return $sourceFinancement;
    }

    /**
     * @Rest\Get(path="/{id}", name="source_financement_show",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_SourceFinancement_SHOW")
     */
    public function show(SourceFinancement $sourceFinancement): SourceFinancement    {
        return $sourceFinancement;
    }

    
    /**
     * @Rest\Put(path="/{id}/edit", name="source_financement_edit",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_SourceFinancement_EDIT")
     */
    public function edit(Request $request, SourceFinancement $sourceFinancement): SourceFinancement    {
        $form = $this->createForm(SourceFinancementType::class, $sourceFinancement);
        $form->submit(Utils::serializeRequestContent($request));

        $this->getDoctrine()->getManager()->flush();

        return $sourceFinancement;
    }
    
    /**
     * @Rest\Put(path="/{id}/clone", name="source_financement_clone",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_SourceFinancement_CLONE")
     */
    public function cloner(Request $request, SourceFinancement $sourceFinancement):  SourceFinancement {
        $em=$this->getDoctrine()->getManager();
        $sourceFinancementNew=new SourceFinancement();
        $form = $this->createForm(SourceFinancementType::class, $sourceFinancementNew);
        $form->submit(Utils::serializeRequestContent($request));
        $em->persist($sourceFinancementNew);

        $em->flush();

        return $sourceFinancementNew;
    }

    /**
     * @Rest\Delete("/{id}", name="source_financement_delete",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_SourceFinancement_DELETE")
     */
    public function delete(SourceFinancement $sourceFinancement): SourceFinancement    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($sourceFinancement);
        $entityManager->flush();

        return $sourceFinancement;
    }
    
    /**
     * @Rest\Post("/delete-selection/", name="source_financement_selection_delete")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_SourceFinancement_DELETE")
     */
    public function deleteMultiple(Request $request): array {
        $entityManager = $this->getDoctrine()->getManager();
        $sourceFinancements = Utils::getObjectFromRequest($request);
        if (!count($sourceFinancements)) {
            throw $this->createNotFoundException("Selectionner au minimum un élément à supprimer.");
        }
        foreach ($sourceFinancements as $sourceFinancement) {
            $sourceFinancement = $entityManager->getRepository(SourceFinancement::class)->find($sourceFinancement->id);
            $entityManager->remove($sourceFinancement);
        }
        $entityManager->flush();

        return $sourceFinancements;
    }
}
