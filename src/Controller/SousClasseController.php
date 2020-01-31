<?php

namespace App\Controller;

use App\Entity\SousClasse;
use App\Form\SousClasseType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Utils\Utils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/api/sousClasse")
 */
class SousClasseController extends AbstractController
{
    /**
     * @Rest\Get(path="/", name="sous_classe_index")
     * @Rest\View(StatusCode = 200)
     * @IsGranted("ROLE_SousClasse_INDEX")
     */
    public function index(): array
    {
        $sousClasses = $this->getDoctrine()
            ->getRepository(SousClasse::class)
            ->findAll();

        return count($sousClasses)?$sousClasses:[];
    }

    /**
     * @Rest\Post(Path="/create", name="sous_classe_new")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_SousClasse_CREATE")
     */
    public function create(Request $request): SousClasse    {
        $sousClasse = new SousClasse();
        $form = $this->createForm(SousClasseType::class, $sousClasse);
        $form->submit(Utils::serializeRequestContent($request));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($sousClasse);
        $entityManager->flush();

        return $sousClasse;
    }

    /**
     * @Rest\Get(path="/{id}", name="sous_classe_show",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_SousClasse_SHOW")
     */
    public function show(SousClasse $sousClasse): SousClasse    {
        return $sousClasse;
    }

    
    /**
     * @Rest\Put(path="/{id}/edit", name="sous_classe_edit",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_SousClasse_EDIT")
     */
    public function edit(Request $request, SousClasse $sousClasse): SousClasse    {
        $form = $this->createForm(SousClasseType::class, $sousClasse);
        $form->submit(Utils::serializeRequestContent($request));

        $this->getDoctrine()->getManager()->flush();

        return $sousClasse;
    }
    
    /**
     * @Rest\Put(path="/{id}/clone", name="sous_classe_clone",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_SousClasse_CLONE")
     */
    public function cloner(Request $request, SousClasse $sousClasse):  SousClasse {
        $em=$this->getDoctrine()->getManager();
        $sousClasseNew=new SousClasse();
        $form = $this->createForm(SousClasseType::class, $sousClasseNew);
        $form->submit(Utils::serializeRequestContent($request));
        $em->persist($sousClasseNew);

        $em->flush();

        return $sousClasseNew;
    }

    /**
     * @Rest\Delete("/{id}", name="sous_classe_delete",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_SousClasse_DELETE")
     */
    public function delete(SousClasse $sousClasse): SousClasse    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($sousClasse);
        $entityManager->flush();

        return $sousClasse;
    }
    
    /**
     * @Rest\Post("/delete-selection/", name="sous_classe_selection_delete")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_SousClasse_DELETE")
     */
    public function deleteMultiple(Request $request): array {
        $entityManager = $this->getDoctrine()->getManager();
        $sousClasses = Utils::getObjectFromRequest($request);
        if (!count($sousClasses)) {
            throw $this->createNotFoundException("Selectionner au minimum un élément à supprimer.");
        }
        foreach ($sousClasses as $sousClasse) {
            $sousClasse = $entityManager->getRepository(SousClasse::class)->find($sousClasse->id);
            $entityManager->remove($sousClasse);
        }
        $entityManager->flush();

        return $sousClasses;
    }
}
