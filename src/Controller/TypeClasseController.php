<?php

namespace App\Controller;

use App\Entity\TypeClasse;
use App\Form\TypeClasseType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Utils\Utils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/api/typeClasse")
 */
class TypeClasseController extends AbstractController
{
    /**
     * @Rest\Get(path="/", name="type_classe_index")
     * @Rest\View(StatusCode = 200)
     * @IsGranted("ROLE_TypeClasse_INDEX")
     */
    public function index(): array
    {
        $typeClasses = $this->getDoctrine()
            ->getRepository(TypeClasse::class)
            ->findAll();

        return count($typeClasses)?$typeClasses:[];
    }

    /**
     * @Rest\Post(Path="/create", name="type_classe_new")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_TypeClasse_CREATE")
     */
    public function create(Request $request): TypeClasse    {
        $typeClasse = new TypeClasse();
        $form = $this->createForm(TypeClasseType::class, $typeClasse);
        $form->submit(Utils::serializeRequestContent($request));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($typeClasse);
        $entityManager->flush();

        return $typeClasse;
    }

    /**
     * @Rest\Get(path="/{id}", name="type_classe_show",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_TypeClasse_SHOW")
     */
    public function show(TypeClasse $typeClasse): TypeClasse    {
        return $typeClasse;
    }

    
    /**
     * @Rest\Put(path="/{id}/edit", name="type_classe_edit",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_TypeClasse_EDIT")
     */
    public function edit(Request $request, TypeClasse $typeClasse): TypeClasse    {
        $form = $this->createForm(TypeClasseType::class, $typeClasse);
        $form->submit(Utils::serializeRequestContent($request));

        $this->getDoctrine()->getManager()->flush();

        return $typeClasse;
    }
    
    /**
     * @Rest\Put(path="/{id}/clone", name="type_classe_clone",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_TypeClasse_CLONE")
     */
    public function cloner(Request $request, TypeClasse $typeClasse):  TypeClasse {
        $em=$this->getDoctrine()->getManager();
        $typeClasseNew=new TypeClasse();
        $form = $this->createForm(TypeClasseType::class, $typeClasseNew);
        $form->submit(Utils::serializeRequestContent($request));
        $em->persist($typeClasseNew);

        $em->flush();

        return $typeClasseNew;
    }

    /**
     * @Rest\Delete("/{id}", name="type_classe_delete",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_TypeClasse_DELETE")
     */
    public function delete(TypeClasse $typeClasse): TypeClasse    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($typeClasse);
        $entityManager->flush();

        return $typeClasse;
    }
    
    /**
     * @Rest\Post("/delete-selection/", name="type_classe_selection_delete")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_TypeClasse_DELETE")
     */
    public function deleteMultiple(Request $request): array {
        $entityManager = $this->getDoctrine()->getManager();
        $typeClasses = Utils::getObjectFromRequest($request);
        if (!count($typeClasses)) {
            throw $this->createNotFoundException("Selectionner au minimum un élément à supprimer.");
        }
        foreach ($typeClasses as $typeClasse) {
            $typeClasse = $entityManager->getRepository(TypeClasse::class)->find($typeClasse->id);
            $entityManager->remove($typeClasse);
        }
        $entityManager->flush();

        return $typeClasses;
    }
}
