<?php

namespace App\Controller;

use App\Entity\EtatMarche;
use App\Entity\TypePassation;
use App\Entity\User;
use App\Form\EtatMarcheType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Utils\Utils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/api/etatMarche")
 */
class EtatMarcheController extends AbstractController
{
    /**
     * @Rest\Get(path="/", name="etat_marche_index")
     * @Rest\View(StatusCode = 200)
     * @IsGranted("ROLE_EtatMarche_INDEX")
     */
    public function index(): array
    {
        $etatMarches = $this->getDoctrine()
            ->getRepository(EtatMarche::class)
            ->findAll();

        return count($etatMarches) ? $etatMarches : [];
    }

    /**
     * @Rest\Get(path="/etatMarche_by_typePassaton/{id}", name="etat_marche_by_typePassation")
     * @Rest\View(StatusCode = 200)
     * @IsGranted("ROLE_EtatMarche_INDEX")
     */

     public function getEtatMarcheByTypePassation(TypePassation $typePassation){
         $em = $this->getDoctrine()->getManager();
         $etatMarches = $em->createQuery('SELECT em FROM App\Entity\EtatMarche em  WHERE em.typePassation=?1')
         ->setParameter(1, $typePassation)
         ->getResult();
         return count($etatMarches) ? $etatMarches : [];

     }

    /**
     * @Rest\Post(Path="/create", name="etat_marche_new")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_EtatMarche_CREATE")
     */
    public function create(Request $request): EtatMarche
    {
        $etatMarche = new EtatMarche();
        $form = $this->createForm(EtatMarcheType::class, $etatMarche);
        $form->submit(Utils::serializeRequestContent($request));

        $entityManager = $this->getDoctrine()->getManager();

        $searchedEtatMarcheByCode = $entityManager->getRepository(EtatMarche::class)
            ->findOneByCode($etatMarche->getCode());

        if ($searchedEtatMarcheByCode) {
            throw $this->createAccessDeniedException("Un Etat Marche avec le même code existe déjà.");
        }

        $searchedEtatMarcheByLabel = $entityManager->getRepository(EtatMarche::class)
            ->findOneByLibelle($etatMarche->getLibelle());

        if ($searchedEtatMarcheByLabel) {
            throw $this->createAccessDeniedException("Un Etat Marche avec le même libelle existe déjà.");
        }

        $etatPrec = $etatMarche->getEtatSuivant();

        if ($etatMarche === $etatPrec) {
            throw $this->createAccessDeniedException("Veuillez choisir un autre etat comme étant celui précédent.");
        }

        if ($etatPrec && $etatPrec->getEtatSuivant()) {
            throw $this->createAccessDeniedException("L'etat que vous avez choisie à déjà un Etat Suivant");
        }

        $entityManager = $this->getDoctrine()->getManager();
        $etatMarche->setEtatSuivant(null);
        $entityManager->persist($etatMarche);
        $entityManager->flush();
        if ($etatPrec) {
            $etatPrec->setEtatSuivant($etatMarche);
            $entityManager->flush();
        }
        return $etatMarche;
    }

    /**
     * @Rest\Get(path="/{id}", name="etat_marche_show",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_EtatMarche_SHOW")
     */
    public function show(EtatMarche $etatMarche): EtatMarche
    {
        return $etatMarche;
    }


    /**
     * @Rest\Put(path="/{id}/edit", name="etat_marche_edit",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_EtatMarche_EDIT")
     */
    public function edit(Request $request, EtatMarche $etatMarche): EtatMarche
    {
        $form = $this->createForm(EtatMarcheType::class, $etatMarche);
        $form->submit(Utils::serializeRequestContent($request));

        $targetEtat = $this->getDoctrine()->getManager()
            ->createQuery(
                'SELECT etatMarche FROM App\Entity\EtatMarche etatMarche
                 WHERE (etatMarche.code=:code OR etatMarche.libelle=:label) AND etatMarche!=:etatMarche
            ')->setParameter('code', $etatMarche->getCode())
            ->setParameter('label', $etatMarche->getLibelle())
            ->setParameter('etatMarche', $etatMarche)
            ->getResult();

        if ($targetEtat) {
            if ($targetEtat[0]->getCode() == $etatMarche->getCode()) {
                throw $this->createAccessDeniedException("Ce code existe déjà.");
            }

            if ($targetEtat[0]->getLibelle() == $etatMarche->getLibelle()) {
                throw  $this->createAccessDeniedException("Ce libelle existe déjà.");
            }
        }

        $etatSuiv = $etatMarche->getEtatSuivant();
        if ($etatSuiv === $etatMarche) {
            throw $this->createAccessDeniedException("Veuillez choisir un autre etat comme étant celui précendent.");
        }

        if ($etatSuiv && $etatSuiv->getEtatSuivant() && ($etatSuiv !== $etatMarche->getEtatSuivant())) {
            throw $this->createAccessDeniedException("L'etat que vous avez choisie à déjà un Etat Suivant.");
        }

        if ($etatSuiv) {
            $etatSuiv->setEtatSuivant($etatMarche);
            $this->getDoctrine()->getManager()->flush();
        }

        return $etatMarche;
    }

    /**
     * @Rest\Put(path="/{id}/clone", name="etat_marche_clone",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_EtatMarche_CLONE")
     */
    public function cloner(Request $request, EtatMarche $etatMarche): EtatMarche
    {
        $em = $this->getDoctrine()->getManager();
        $etatMarcheNew = new EtatMarche();
        $form = $this->createForm(EtatMarcheType::class, $etatMarcheNew);
        $form->submit(Utils::serializeRequestContent($request));

        $searchedEtatMarcheByCode = $em->getRepository(EtatMarche::class)
            ->findOneByCode($etatMarcheNew->getCode());

        if ($searchedEtatMarcheByCode) {
            throw $this->createAccessDeniedException("Un Etat Marche avec le même code existe déjà.");
        }

        $searchedEtatMarcheByLabel = $em->getRepository(EtatMarche::class)
            ->findOneByLibelle($etatMarcheNew->getLibelle());

        if ($searchedEtatMarcheByLabel) {
            throw $this->createAccessDeniedException("Un Etat Marche avec le même libelle existe déjà.");
        }
        $etatPrec = $etatMarcheNew->getEtatSuivant();
        if ($etatPrec === $etatMarcheNew) {
            throw $this->createAccessDeniedException("Veuillez choisir un autre etat comme étant celui précendent.");
        }

        if ($etatPrec && $etatPrec->getEtatSuivant()) {
            throw $this->createAccessDeniedException("L'etat que vous avez choisie à déjà un Etat Suivant");
        }

        $etatMarcheNew->setEtatSuivant(null);
        $em->persist($etatMarcheNew);
        $em->flush();
        if ($etatPrec) {
            $etatPrec->setEtatSuivant($etatMarcheNew);
            $em->flush();
        }
        return $etatMarcheNew;
    }

    /**
     * @Rest\Delete("/{id}", name="etat_marche_delete",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_EtatMarche_DELETE")
     */
    public function delete(EtatMarche $etatMarche): EtatMarche
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($etatMarche);
        $entityManager->flush();

        return $etatMarche;
    }

    /**
     * @Rest\Post("/delete-selection/", name="etat_marche_selection_delete")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_EtatMarche_DELETE")
     */
    public function deleteMultiple(Request $request): array
    {
        $entityManager = $this->getDoctrine()->getManager();
        $etatMarches = Utils::getObjectFromRequest($request);

        if (!count($etatMarches)) {
            throw $this->createNotFoundException("Selectionner au minimum un élément à supprimer.");
        }
        foreach ($etatMarches as $etatMarche) {
            $etatMarche = $entityManager->getRepository(EtatMarche::class)->find($etatMarche->id);
            $entityManager->remove($etatMarche);
            $entityManager->flush();
        }

        return $etatMarches;
    }

    /**
     * @Rest\Get(path="/{id}/users", name="not_added_users")
     * @Rest\View(statusCode=200)
     * @IsGranted("ROLE_EtatMarche_INDEX")
     */
    public function fetchNotAddedUser(EtatMarche $etatMarche)
    {
        $addedUsers = $etatMarche->getUsers()->toArray();
        $users = $this->getDoctrine()->getRepository(User::class)
            ->findAll();
        $notAddedUser = [];
        if(!count($addedUsers)) {
            return $users;
        }
        foreach ($users as $user) {
            if(!in_array($user, $addedUsers)) {
                $notAddedUser[] = $user;
            }
        }
        return $notAddedUser;
    }
}
