<?php

namespace App\Controller;

use App\Entity\Compte;
use App\Entity\CompteDivisionnaire;
use App\Entity\TypeClasse;
use App\Form\CompteType;
use Doctrine\ORM\EntityManagerInterface;
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
     * @Rest\Post(Path="/create-multiple", name="compte_multiple_new")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Compte_CREATE")
     */
    public function createMultiple(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $accounts = Utils::serializeRequestContent($request);

        $createdAccounts = [];
        foreach ($accounts as $account) {
            $retrievedAccount = new Compte();
            $form = $this->createForm(CompteType::class, $retrievedAccount);
            $form->submit($account);
            $searchedAccountByNumero = $em->getRepository(Compte::class)
                ->findOneByNumero($retrievedAccount->getNumero());
            if($searchedAccountByNumero) {
                throw $this->createAccessDeniedException("Ce code est déjà celui d'un compte");
            }
            $searchedAccountByLabel = $em->getRepository(Compte::class)
                ->findOneByLibelle($retrievedAccount->getLibelle());
            if($searchedAccountByLabel) {
                throw $this->createAccessDeniedException("Ce libelle est déjà celui d'un compte");
            }
            $em->persist($retrievedAccount);
            $createdAccounts[] = $retrievedAccount;
        }
        $em->flush();
        return $createdAccounts;
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
     * @Rest\Get(path="/recette", name="compte_recette_list")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Compte_SHOW")
     */
    public function fetchRecetteCompte(Request $request, EntityManagerInterface $entityManager) {
        return $entityManager->createQuery(
            'SELECT c 
            FROM App\Entity\Compte c 
            JOIN c.compteDivisionnaire cd 
            JOIN cd.sousClasse scl
            JOIN scl.classe cl WHERE cl.typeClasse IN (SELECT type FROM App\Entity\TypeClasse type WHERE type.code=1)
            '
        )->getResult();
    }

    /**
     * @Rest\Get(path="/depense", name="compte_depense_show")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Compte_SHOW")
     */
    public function fetchDepenseCompte(Request $request, EntityManagerInterface $entityManager) {
        return $entityManager->createQuery(
            'SELECT c 
            FROM App\Entity\Compte c 
            JOIN c.compteDivisionnaire cd 
            JOIN cd.sousClasse scl
            JOIN scl.classe cl WHERE cl.typeClasse IN (SELECT type FROM App\Entity\TypeClasse type WHERE type.code=2)
            '
        )->getResult();
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

    /**
     * @Rest\Get(path="/{id}/compte-divisionnaire", name="compte_divisionnaire")
     * @Rest\View(StatusCode = 200)
     * @IsGranted("ROLE_Compte_INDEX")
     */

    public function findByCompteDivisionnaire (CompteDivisionnaire $compteDivisionnaire)
    {
        $comptes = $this->getDoctrine()
            ->getRepository(Compte::class)
            ->findByCompteDivisionnaire($compteDivisionnaire);

        return count($comptes)?$comptes:[];
    }

}
