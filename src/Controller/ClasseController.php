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

/**
 * @Route("/api/classe")
 */
class ClasseController extends AbstractController
{
    /**
     * @Rest\Get(path="/", name="classe_index")
     * @Rest\View(StatusCode = 200)
     * @IsGranted("ROLE_Classe_INDEX")
     */
    public function index(): array
    {
        $classes = $this->getDoctrine()
            ->getRepository(Classe::class)
            ->findAll();

        return count($classes)?$classes:[];
    }

    /**
     * @Rest\Post(Path="/create", name="classe_new")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Classe_CREATE")
     */
    public function create(Request $request): Classe    {
        $classe = new Classe();
        $form = $this->createForm(ClasseType::class, $classe);
        $form->submit(Utils::serializeRequestContent($request));
        $entityManager = $this->getDoctrine()->getManager();
         
        // check if code already exist
        $searchedClasseByNumero = $entityManager->getRepository(Classe::class)->findByNumero($classe->getNumero());
        if(count($searchedClasseByNumero)) {
             throw $this->createAccessDeniedException("Une classe avec le même numéro existe déjà, merci de changer de numéro...");
        }
        // check if libelle alredy exit
        $searchedClasseByLibelle = $entityManager->getRepository(Classe::class)->findByLibelle($classe->getLibelle());
        if(count($searchedClasseByLibelle)) {
             throw $this->createAccessDeniedException("Une classe avec le même libellé existe déjà, merci de changer de libellé...");
        }
        $entityManager->persist($classe);
        $entityManager->flush();

        return $classe;
    }

    /**
     * @Rest\Get(path="/{id}", name="classe_show",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Classe_SHOW")
     */
    public function show(Classe $classe): Classe    {
        return $classe;
    }

    
    /**
     * @Rest\Put(path="/{id}/edit", name="classe_edit",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Classe_EDIT")
     */
    public function edit(Request $request, Classe $classe): Classe    {
        $form = $this->createForm(ClasseType::class, $classe);
        $form->submit(Utils::serializeRequestContent($request));

        $this->getDoctrine()->getManager()->flush();

        return $classe;
    }
    
    /**
     * @Rest\Put(path="/{id}/clone", name="classe_clone",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Classe_CLONE")
     */
    public function cloner(Request $request, Classe $classe):  Classe {
        $em=$this->getDoctrine()->getManager();
        $classeNew=new Classe();
        $form = $this->createForm(ClasseType::class, $classeNew);
        $form->submit(Utils::serializeRequestContent($request));
        // check if code already exist
        $searchedClasseByNumero = $em->getRepository(Classe::class)->findByNumero($classeNew->getNumero());
        if(count($searchedClasseByNumero)) {
             throw $this->createAccessDeniedException("Une classe avec le même numéro existe déjà, merci de changer de numéro...");
        }
        // check if libelle alredy exit
        $searchedClasseByLibelle = $em->getRepository(Classe::class)->findByLibelle($classeNew->getLibelle());
        if(count($searchedClasseByLibelle)) {
             throw $this->createAccessDeniedException("Une classe avec le même libellé existe déjà, merci de changer de libellé...");
        }
        $em->persist($classeNew);

        $em->flush();

        return $classeNew;
    }

    /**
     * @Rest\Delete("/{id}", name="classe_delete",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Classe_DELETE")
     */
    public function delete(Classe $classe): Classe    {
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
}
