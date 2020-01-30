<?php

namespace App\Controller;

use App\Entity\Compte;
use App\Form\CompteType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Utils\Utils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/api/compte")
 */
class CompteController extends AbstractController
{
    /**
     * @Rest\Get(path="/", name="compte_index")
     * @Rest\View(StatusCode = 200)
     * @IsGranted("ROLE_Compte_INDEX")
     */
    public function index(): array
    {
        $comptes = $this->getDoctrine()
            ->getRepository(Compte::class)
            ->findAll();

        return count($comptes)?$comptes:[];
    }

    /**
     * @Rest\Post(Path="/create", name="compte_new")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Compte_CREATE")
     */
    public function create(Request $request): Compte    {
        $compte = new Compte();
        $form = $this->createForm(CompteType::class, $compte);
        $form->submit(Utils::serializeRequestContent($request));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($compte);
        $entityManager->flush();

        return $compte;
    }

    /**
     * @Rest\Get(path="/{id}", name="compte_show",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Compte_SHOW")
     */
    public function show(Compte $compte): Compte    {
        return $compte;
    }

    
    /**
     * @Rest\Put(path="/{id}/edit", name="compte_edit",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Compte_EDIT")
     */
    public function edit(Request $request, Compte $compte): Compte    {
        $form = $this->createForm(CompteType::class, $compte);
        $form->submit(Utils::serializeRequestContent($request));

        $this->getDoctrine()->getManager()->flush();

        return $compte;
    }
    
    /**
     * @Rest\Put(path="/{id}/clone", name="compte_clone",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Compte_CLONE")
     */
    public function cloner(Request $request, Compte $compte):  Compte {
        $em=$this->getDoctrine()->getManager();
        $compteNew=new Compte();
        $form = $this->createForm(CompteType::class, $compteNew);
        $form->submit(Utils::serializeRequestContent($request));
        $em->persist($compteNew);

        $em->flush();

        return $compteNew;
    }

    /**
     * @Rest\Delete("/{id}", name="compte_delete",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Compte_DELETE")
     */
    public function delete(Compte $compte, TracelogController $controller): Compte    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($compte);
        $entityManager->flush();
        return $compte;
    }
    
    /**
     * @Rest\Post("/delete-selection/", name="compte_selection_delete")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Compte_DELETE")
     */
    public function deleteMultiple(Request $request): array {
        $entityManager = $this->getDoctrine()->getManager();
        $comptes = Utils::getObjectFromRequest($request);
        if (!count($comptes)) {
            throw $this->createNotFoundException("Selectionner au minimum un élément à supprimer.");
        }
        foreach ($comptes as $compte) {
            $compte = $entityManager->getRepository(Compte::class)->find($compte->id);
            $entityManager->remove($compte);
        }
        $entityManager->flush();

        return $comptes;
    }
}
