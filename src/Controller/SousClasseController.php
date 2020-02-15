<?php

namespace App\Controller;

use App\Entity\SousClasse;
use App\Entity\Classe;
use App\Form\SousClasseType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Utils\Utils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Doctrine\ORM\EntityManagerInterface;

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

        return count($sousClasses) ? $sousClasses : [];
    }

    /**
     * @Rest\Post(Path="/create", name="sous_classe_new")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_SousClasse_CREATE")
     */
    public function create(Request $request): SousClasse
    {
        $sousClasse = new SousClasse();
        $form = $this->createForm(SousClasseType::class, $sousClasse);
        $form->submit(Utils::serializeRequestContent($request));
        $entityManager = $this->getDoctrine()->getManager();
        // check if numero and libelle already exist
        $this->checkNumeroAndLibelle($sousClasse, $entityManager);

        $entityManager->persist($sousClasse);
        $entityManager->flush();

        return $sousClasse;
    }

    /**
     * @Rest\Post(path="/create-multiple", name="create_multiple_subclass")
     * @Rest\View(statusCode=201)
     * @IsGranted("ROLE_SousClasse_CREATE")
     */
    public function createMultiple(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $subClasses = Utils::serializeRequestContent($request);

        $createdItems = [];
        foreach ($subClasses as $currentSubClass) {
            $subClass = new SousClasse();
            $form = $this->createForm(SousClasseType::class, $subClass);
            $form->submit($currentSubClass);
            $searchedSubClass = $em->getRepository(SousClasse::class)
                ->findOneBy(['numero' => $subClass->getNumero()]);
            if($searchedSubClass) {
                throw $this->createAccessDeniedException('Ce code est déjà celui d\'une sous classe.');
            }
            $em->persist($subClass);
            $createdItems[] = $subClass;
        }

        $em->flush();
        return $createdItems;
    }

    /**
     * @Rest\Get(path="/{id}", name="sous_classe_show",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_SousClasse_SHOW")
     */
    public function show(SousClasse $sousClasse): SousClasse
    {
        return $sousClasse;
    }


    /**
     * @Rest\Put(path="/{id}/edit", name="sous_classe_edit",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_SousClasse_EDIT")
     */
    public function edit(Request $request, SousClasse $sousClasse, EntityManagerInterface $em): SousClasse
    {
        $form = $this->createForm(SousClasseType::class, $sousClasse);
        $form->submit(Utils::serializeRequestContent($request));
        $this->checkEditcSousclasseNumeroAndLibelle($sousClasse, $em);


        $em->flush();

        return $sousClasse;
    }

    /**
     * @Rest\Put(path="/{id}/clone", name="sous_classe_clone",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_SousClasse_CLONE")
     */
    public function cloner(Request $request, SousClasse $sousClasse): SousClasse
    {
        $em = $this->getDoctrine()->getManager();
        $sousClasseNew = new SousClasse();
        $form = $this->createForm(SousClasseType::class, $sousClasseNew);
        $form->submit(Utils::serializeRequestContent($request));
        ////
        $this->checkNumeroAndLibelle($sousClasseNew, $em);
        $em->persist($sousClasseNew);

        $em->flush();

        return $sousClasseNew;
    }

    /**
     * @Rest\Delete("/{id}", name="sous_classe_delete",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_SousClasse_DELETE")
     */
    public function delete(SousClasse $sousClasse): SousClasse
    {
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
    public function deleteMultiple(Request $request): array
    {
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

    /////////////////////////////Les Testes////////////////////////////////////

    public function checkNumeroAndLibelle(SousClasse $sousclasse, EntityManagerInterface $em)
    {
        $searchedsSousclasseByNumero = $em->getRepository(SousClasse::class)->findByNumero($sousclasse->getNumero());
        if (count($searchedsSousclasseByNumero)) {
            throw $this->createAccessDeniedException("Une sous classe avec le même numéro existe déjà, merci de changer de numéro...");
        }
        // check if libelle alredy exit
        $searchedSousclasseByLibelle = $em->getRepository(SousClasse::class)->findByLibelle($sousclasse->getLibelle());
        if (count($searchedSousclasseByLibelle)) {
            throw $this->createAccessDeniedException("Une sous classe avec le même libellé existe déjà, merci de changer de libellé...");
        }
    }

    /*public function checkClasse(SousClasse $sousclasse){
        $recoverClasse = $this->getDoctrine()->getManager()
                ->createQuery(
                        'SELECT sousclasse From App\Entity\SousClasse sousclasse
                            WHERE (sousclasse.classe=:classe)
                              ')-> setParameter('classe',$sousclasse->getClasse())
                               ->getResult();
                         if($recoverClasse){
                             if(count($recoverClasse)!=0){
                                  throw $this->createAccessDeniedException("Une sous classe  avec la même classe existe déjà.");
                             }
                         }
        
    }*/
    public function checkEditcSousclasseNumeroAndLibelle(SousClasse $sousclasse, EntityManagerInterface $em)
    {
        $searchedSousclasseByCode = $em->createQuery("select c from App\Entity\SousClasse c where c != :sousclasse and c.numero = :num")
            ->setParameter('sousclasse', $sousclasse)->setParameter('num', $sousclasse->getNumero())->getResult();
        if (count($searchedSousclasseByCode)) {
            throw $this->createAccessDeniedException("Une sous classe avec le même numéro existe déjà, merci de changer de numéro...");
        }

        // check if libelle already exist
        $searchedSousclasseByLibelle = $em->createQuery("select c from App\Entity\SousClasse c where c != :sousclasse and c.libelle = :lib")
            ->setParameter('sousclasse', $sousclasse)->setParameter('lib', $sousclasse->getLibelle())->getResult();
        if (count($searchedSousclasseByLibelle)) {
            throw $this->createAccessDeniedException("Une sous classe avec le même libellé existe déjà, merci de changer de libellé...");
        }
    }

    /**
     * @Rest\Get(path="/{id}/classe", name="classe")
     * @Rest\View(StatusCode = 200)
     * @IsGranted("ROLE_SousClasse_INDEX")
     */

    public function findByClasse( Classe $classe)
    {
        $sousClasses = $this->getDoctrine()
            ->getRepository(SousClasse::class)
            ->findByClasse($classe);

        return count($sousClasses)?$sousClasses:[];
    }
}

