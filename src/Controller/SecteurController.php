<?php

namespace App\Controller;


use App\Entity\Secteur;
use App\Form\SecteurType;
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
    public function create(Request $request): Secteur    {
        $secteur = new Secteur();
        $form = $this->createForm(SecteurType::class, $secteur);
        $form->submit(Utils::serializeRequestContent($request));

        $entityManager = $this->getDoctrine()->getManager();

        $searchedSectorByCode = $entityManager->getRepository(Secteur::class)
            ->findOneByCode($secteur->getCode());
        if ($searchedSectorByCode) {
            throw $this->createAccessDeniedException("Un secteur avec ce code existe déjà.");
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
    public function edit(Request $request, Secteur $secteur): Secteur    {
        $form = $this->createForm(SecteurType::class, $secteur);
        $form->submit(Utils::serializeRequestContent($request));

        $targetSecteur = $this->getDoctrine()->getManager()
            ->createQuery(
                'SELECT secteur FROM App\Entity\Secteur secteur
                 WHERE (secteur.code=:code) AND secteur!=:secteur
            ')->setParameter('code', $secteur->getCode())
            ->setParameter('secteur', $secteur)
            ->getResult();
        if($targetSecteur) {
            if ($targetSecteur[0]->getCode() == $secteur->getCode()) {
                throw $this->createAccessDeniedException("Ce code existe déjà.");
            }
        }

        $this->getDoctrine()->getManager()->flush();
        return $secteur;
    }
    
    /**
     * @Rest\Put(path="/{id}/clone", name="secteur_clone",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Secteur_CLONE")
     */
    public function cloner(Request $request, Secteur $secteur):  Secteur {
        $em=$this->getDoctrine()->getManager();
        $secteurNew=new Secteur();

        $form = $this->createForm(SecteurType::class, $secteurNew);
        $form->submit(Utils::serializeRequestContent($request));


        $searchedSectorByCode = $em->getRepository(Secteur::class)
            ->findOneByCode($secteur->getCode());
        if ($searchedSectorByCode) {
            throw $this->createAccessDeniedException("Un secteur avec ce code existe déjà.");
        }

        $em->persist($secteurNew);
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
     /**
     * @Rest\Get(path="/secteurs_fournisseur", name="secteurs_fournisseur")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Fournisseur_INDEX")
     */
    public function findWithAtLeastOneFournisseur()
    { 
        $em = $this->getDoctrine()->getManager();
        $secteur = $em->createQuery('select s from App\Entity\Secteur s where s.fournisseurs => 1')
                ->getResult();

        return $secteur;
    }
     
}
