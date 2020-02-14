<?php

namespace App\Controller;

use App\Entity\Classe;
use App\Form\ClasseType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Utils\Utils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @Route("/api/classe")
 */
class ClasseController extends AbstractController {

    /**
     * @Rest\Get(path="/", name="classe_index")
     * @Rest\View(StatusCode = 200)
     * @IsGranted("ROLE_Classe_INDEX")
     */
    public function index(): array {
        $classes = $this->getDoctrine()
            ->getRepository(Classe::class)
            ->findAll();

        return count($classes) ? $classes : [];
    }

    /**
     * @Rest\Post(Path="/create", name="classe_new")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Classe_CREATE")
     */
    public function create(Request $request): Classe {
        $classe = new Classe();
        $form = $this->createForm(ClasseType::class, $classe);
        $form->submit(Utils::serializeRequestContent($request));
        $entityManager = $this->getDoctrine()->getManager();

        // check if numero and libelle already exist
        $this->checkNumeroAndLibelle($classe, $entityManager);

        $entityManager->persist($classe);
        $entityManager->flush();

        return $classe;
    }

    /**
     * @Rest\Get(path="/{id}", name="classe_show",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Classe_SHOW")
     */
    public function show(Classe $classe): Classe {
        return $classe;
    }

    /**
     * @Rest\Put(path="/{id}/edit", name="classe_edit",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Classe_EDIT")
     */
    public function edit(Request $request, Classe $classe, EntityManagerInterface $em) {
        $form = $this->createForm(ClasseType::class, $classe);
        $form->submit(Utils::serializeRequestContent($request));

        // check if numero and libelle already exist
        $this->checkEditClasseNumeroAndLibelle($classe, $em);

        $em->flush();

        return $classe;
    }

    /**
     * @Rest\Put(path="/{id}/clone", name="classe_clone",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Classe_CLONE")
     */
    public function cloner(Request $request, Classe $classe): Classe {
        $em = $this->getDoctrine()->getManager();
        $classeNew = new Classe();
        $form = $this->createForm(ClasseType::class, $classeNew);
        $form->submit(Utils::serializeRequestContent($request));
        // check if numero already exist
        $this->checkNumeroAndLibelle($classeNew, $em);
        $em->persist($classeNew);

        $em->flush();

        return $classeNew;
    }

    /**
     * @Rest\Delete("/{id}", name="classe_delete",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Classe_DELETE")
     */
    public function delete(Classe $classe): Classe {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($classe);
        $entityManager->flush();

        return $classe;
    }

    /**
     * @Rest\Post("/delete-selection/", name="classe_selection_delete")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Classe_DELETE")
     */
    public function deleteMultiple(Request $request): array {
        $entityManager = $this->getDoctrine()->getManager();
        $classes = Utils::getObjectFromRequest($request);
        if (!count($classes)) {
            throw $this->createNotFoundException("Selectionner au minimum un élément à supprimer.");
        }
        foreach ($classes as $classe) {
            $classe = $entityManager->getRepository(Classe::class)->find($classe->id);
            $entityManager->remove($classe);
        }
        $entityManager->flush();

        return $classes;
    }

    //////////////////////////////////////// Tests /////////////////////////////////////////////

    public function checkEditClasseNumeroAndLibelle(Classe $classe, EntityManagerInterface $em) {
        $searchedClasseByCode = $em->createQuery("select c from App\Entity\Classe c where c != :classe and c.numero = :num")->setParameter('classe', $classe)->setParameter('num', $classe->getNumero())->getResult();
        if (count($searchedClasseByCode)) {
            throw $this->createAccessDeniedException("Une classe avec le même numéro existe déjà, merci de changer de numéro...");
        }

        // check if libelle already exist
        $searchedClasseByLibelle = $em->createQuery("select c from App\Entity\Classe c where c != :classe and c.libelle = :lib")->setParameter('classe', $classe)->setParameter('lib', $classe->getLibelle())->getResult();
        if (count($searchedClasseByLibelle)) {
            throw $this->createAccessDeniedException("Une classe avec le même libellé existe déjà, merci de changer de libellé...");
        }
    }


    public function checkNumeroAndLibelle(Classe $classe, EntityManagerInterface $em) {
        $searchedClasseByNumero = $em->getRepository(Classe::class)->findByNumero($classe->getNumero());
        if (count($searchedClasseByNumero)) {
            throw $this->createAccessDeniedException("Une classe avec le même numéro existe déjà, merci de changer de numéro...");
        }
        // check if libelle alredy exit
        $searchedClasseByLibelle = $em->getRepository(Classe::class)->findByLibelle($classe->getLibelle());
        if (count($searchedClasseByLibelle)) {
            throw $this->createAccessDeniedException("Une classe avec le même libellé existe déjà, merci de changer de libellé...");
        }
    }

    // l’enlèvement de l'unicité sur catégorie et type classe lors du crud de classe
/**
    public function checkCategorieAndType(Classe $classe){
        $targetClasse = $this->getDoctrine()->getManager()
            ->createQuery(
                'SELECT classe FROM App\Entity\Classe classe
                 WHERE (classe.categorieClasse=:categorie AND classe.typeClasse=:type) 
            ')->setParameter('categorie', $classe->getCategorieClasse())
            ->setParameter('type', $classe->getTypeClasse())
            ->getResult();

        if($targetClasse){
            if(count($targetClasse)!=0){
                throw $this->createAccessDeniedException("Une classe avec les mêmes catégorie et type existent déjà.");
            }
        }
    }
 **/
}
