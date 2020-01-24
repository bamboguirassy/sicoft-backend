<?php

namespace App\Controller;

use App\Entity\Secteur;
use App\Form\SecteurType;
use App\Repository\SecteurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Utils\Utils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/api/secteur")
 */
class SecteurController extends AbstractController
{
    /**
     * @Rest\Get(path="/", name="secteur_index")
     * @Rest\View(StatusCode = 200)
     * @IsGranted("ROLE_Secteur_INDEX")
     */
    public function index(): array
    {
        $secteurs = $this->getDoctrine()
            ->getRepository(Secteur::class)
            ->findAll();

        return count($secteurs)?$secteurs:[];
    }

    /**
     * @Rest\Post(Path="/create", name="secteur_new")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Secteur_CREATE")
     */
    public function create(Request $request, SecteurRepository $secteurRepository): Secteur    {
        $secteur = new Secteur();
        $secteurs = $secteurRepository->findAll();
        $form = $this->createForm(SecteurType::class, $secteur);
        $form->submit(Utils::serializeRequestContent($request));
        $entityManager = $this->getDoctrine()->getManager();
        foreach ($secteurs as $value)
        {
            if ($value->getCode() == $secteur->getCode() )
                throw $this->createNotFoundException("Code du secteur exite déja");
        }
        $entityManager->persist($secteur);
        $entityManager->flush();
        return $secteur;
    }

    /**
     * @Rest\Get(path="/{id}", name="secteur_show",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Secteur_SHOW")
     */
    public function show(Secteur $secteur): Secteur    {
        return $secteur;
    }
    /**
     * @Rest\Put(path="/{id}/edit", name="secteur_edit",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Secteur_EDIT")
     */
    public function edit(Request $request, Secteur $secteur,SecteurRepository $secteurRepository): Secteur    {
        $othersSercteur =[];
        $secteurs = $secteurRepository->findAll();

        foreach ($secteurs as $value)
        {
            if ($value->getCode() !== $secteur->getCode() )
                array_push($othersSercteur, $value);

        }
        $form = $this->createForm(SecteurType::class, $secteur);
        $form->submit(Utils::serializeRequestContent($request));
        foreach ($othersSercteur as $value)
        {
            if ($value->getCode() == $secteur->getCode() )
                throw $this->createNotFoundException("Code du secteur exite déja");
        }
        $this->getDoctrine()->getManager()->flush();
        return $secteur;
    }
    
    /**
     * @Rest\Put(path="/{id}/clone", name="secteur_clone",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Secteur_CLONE")
     */
    public function cloner(Request $request, Secteur $secteur,SecteurRepository $secteurRepository):  Secteur {
        $em=$this->getDoctrine()->getManager();
        $secteurNew=new Secteur();
        $secteurs = $secteurRepository->findAll();
        $form = $this->createForm(SecteurType::class, $secteurNew);
        $form->submit(Utils::serializeRequestContent($request));
        $em->persist($secteurNew);
        foreach ($secteurs as $value)
        {
            if ($value->getCode() == $secteur->getCode() )
                throw $this->createNotFoundException("Code du secteur exite déja");
        }
        $em->flush();

        return $secteurNew;
    }

    /**
     * @Rest\Delete("/{id}", name="secteur_delete",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Secteur_DELETE")
     */
    public function delete(Secteur $secteur): Secteur    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($secteur);
        $entityManager->flush();

        return $secteur;
    }
    
    /**
     * @Rest\Post("/delete-selection/", name="secteur_selection_delete")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Secteur_DELETE")
     */
    public function deleteMultiple(Request $request): array {
        $entityManager = $this->getDoctrine()->getManager();
        $secteurs = Utils::getObjectFromRequest($request);
        if (!count($secteurs)) {
            throw $this->createNotFoundException("Selectionner au minimum un élément à supprimer.");
        }
        foreach ($secteurs as $secteur) {
            $secteur = $entityManager->getRepository(Secteur::class)->find($secteur->id);
            $entityManager->remove($secteur);
        }
        $entityManager->flush();

        return $secteurs;
    }
}
