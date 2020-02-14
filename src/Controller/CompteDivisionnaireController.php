<?php

namespace App\Controller;

use App\Entity\CompteDivisionnaire;
use App\Entity\SousClasse;
use App\Form\CompteDivisionnaireType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Utils\Utils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/api/compteDivisionnaire")
 */
class CompteDivisionnaireController extends AbstractController
{
    /**
     * @Rest\Get(path="/", name="compte_divisionnaire_index")
     * @Rest\View(StatusCode = 200)
     * @IsGranted("ROLE_CompteDivisionnaire_INDEX")
     */
    public function index(): array
    {
        $compteDivisionnaires = $this->getDoctrine()
            ->getRepository(CompteDivisionnaire::class)
            ->findAll();

        return count($compteDivisionnaires)?$compteDivisionnaires:[];
    }

    /**
     * @Rest\Post(Path="/create", name="compte_divisionnaire_new")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_CompteDivisionnaire_CREATE")
     */
    public function create(Request $request): CompteDivisionnaire    {
        $compteDivisionnaire = new CompteDivisionnaire();
        $form = $this->createForm(CompteDivisionnaireType::class, $compteDivisionnaire);
        $form->submit(Utils::serializeRequestContent($request));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($compteDivisionnaire);
        $entityManager->flush();

        return $compteDivisionnaire;
    }

    /**
     * @Rest\Get(path="/{id}", name="compte_divisionnaire_show",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_CompteDivisionnaire_SHOW")
     */
    public function show(CompteDivisionnaire $compteDivisionnaire): CompteDivisionnaire    {
        return $compteDivisionnaire;
    }

    
    /**
     * @Rest\Put(path="/{id}/edit", name="compte_divisionnaire_edit",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_CompteDivisionnaire_EDIT")
     */
    public function edit(Request $request, CompteDivisionnaire $compteDivisionnaire): CompteDivisionnaire    {
        $form = $this->createForm(CompteDivisionnaireType::class, $compteDivisionnaire);
        $form->submit(Utils::serializeRequestContent($request));

        $this->getDoctrine()->getManager()->flush();

        return $compteDivisionnaire;
    }
    
    /**
     * @Rest\Put(path="/{id}/clone", name="compte_divisionnaire_clone",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_CompteDivisionnaire_CLONE")
     */
    public function cloner(Request $request, CompteDivisionnaire $compteDivisionnaire):  CompteDivisionnaire {
        $em=$this->getDoctrine()->getManager();
        $compteDivisionnaireNew=new CompteDivisionnaire();
        $form = $this->createForm(CompteDivisionnaireType::class, $compteDivisionnaireNew);
        $form->submit(Utils::serializeRequestContent($request));
        $em->persist($compteDivisionnaireNew);

        $em->flush();

        return $compteDivisionnaireNew;
    }

    /**
     * @Rest\Delete("/{id}", name="compte_divisionnaire_delete",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_CompteDivisionnaire_DELETE")
     */
    public function delete(CompteDivisionnaire $compteDivisionnaire): CompteDivisionnaire    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($compteDivisionnaire);
        $entityManager->flush();

        return $compteDivisionnaire;
    }
    
    /**
     * @Rest\Post("/delete-selection/", name="compte_divisionnaire_selection_delete")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_CompteDivisionnaire_DELETE")
     */
    public function deleteMultiple(Request $request): array {
        $entityManager = $this->getDoctrine()->getManager();
        $compteDivisionnaires = Utils::getObjectFromRequest($request);
        if (!count($compteDivisionnaires)) {
            throw $this->createNotFoundException("Selectionner au minimum un élément à supprimer.");
        }
        foreach ($compteDivisionnaires as $compteDivisionnaire) {
            $compteDivisionnaire = $entityManager->getRepository(CompteDivisionnaire::class)->find($compteDivisionnaire->id);
            $entityManager->remove($compteDivisionnaire);
        }
        $entityManager->flush();

        return $compteDivisionnaires;
    }

    /**
     * @Rest\Get(path="/{id}/sous-classe", name="sous_classe")
     * @Rest\View(StatusCode = 200)
     * @IsGranted("ROLE_CompteDivisionnaire_INDEX")
     */
    public function findBySousClasse (SousClasse $sousClasse)
    {
        $sousClasses = $this->getDoctrine()
            ->getRepository(CompteDivisionnaire::class)
            ->findBySousClasse($sousClasse);

        return count($sousClasses)?$sousClasses:[];
    }
}
