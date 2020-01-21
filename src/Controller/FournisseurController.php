<?php

namespace App\Controller;

use App\Entity\Fournisseur;
use App\Form\FournisseurType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Utils\Utils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/api/fournisseur")
 */
class FournisseurController extends AbstractController
{
    /**
     * @Rest\Get(path="/", name="fournisseur_index")
     * @Rest\View(StatusCode = 200)
     * @IsGranted("ROLE_Fournisseur_INDEX")
     */
    public function index(): array
    {
        $fournisseurs = $this->getDoctrine()
            ->getRepository(Fournisseur::class)
            ->findAll();

        return count($fournisseurs)?$fournisseurs:[];
    }

    /**
     * @Rest\Post(Path="/create", name="fournisseur_new")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Fournisseur_CREATE")
     */
    public function create(Request $request): Fournisseur    {
        $fournisseur = new Fournisseur();
        $form = $this->createForm(FournisseurType::class, $fournisseur);
        $form->submit(Utils::serializeRequestContent($request));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($fournisseur);
        $entityManager->flush();

        return $fournisseur;
    }

    /**
     * @Rest\Get(path="/{id}", name="fournisseur_show",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Fournisseur_SHOW")
     */
    public function show(Fournisseur $fournisseur): Fournisseur    {
        return $fournisseur;
    }

    
    /**
     * @Rest\Put(path="/{id}/edit", name="fournisseur_edit",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Fournisseur_EDIT")
     */
    public function edit(Request $request, Fournisseur $fournisseur): Fournisseur    {
        $form = $this->createForm(FournisseurType::class, $fournisseur);
        $form->submit(Utils::serializeRequestContent($request));

        $this->getDoctrine()->getManager()->flush();

        return $fournisseur;
    }
    
    /**
     * @Rest\Put(path="/{id}/clone", name="fournisseur_clone",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Fournisseur_CLONE")
     */
    public function cloner(Request $request, Fournisseur $fournisseur):  Fournisseur {
        $em=$this->getDoctrine()->getManager();
        $fournisseurNew=new Fournisseur();
        $form = $this->createForm(FournisseurType::class, $fournisseurNew);
        $form->submit(Utils::serializeRequestContent($request));
        $em->persist($fournisseurNew);

        $em->flush();

        return $fournisseurNew;
    }

    /**
     * @Rest\Delete("/{id}", name="fournisseur_delete",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Fournisseur_DELETE")
     */
    public function delete(Fournisseur $fournisseur): Fournisseur    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($fournisseur);
        $entityManager->flush();

        return $fournisseur;
    }
    
    /**
     * @Rest\Post("/delete-selection/", name="fournisseur_selection_delete")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Fournisseur_DELETE")
     */
    public function deleteMultiple(Request $request): array {
        $entityManager = $this->getDoctrine()->getManager();
        $fournisseurs = Utils::getObjectFromRequest($request);
        if (!count($fournisseurs)) {
            throw $this->createNotFoundException("Selectionner au minimum un élément à supprimer.");
        }
        foreach ($fournisseurs as $fournisseur) {
            $fournisseur = $entityManager->getRepository(Fournisseur::class)->find($fournisseur->id);
            $entityManager->remove($fournisseur);
        }
        $entityManager->flush();

        return $fournisseurs;
    }
}
