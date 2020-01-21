<?php

namespace App\Controller;

use App\Entity\TypeDocument;
use App\Form\TypeDocumentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Utils\Utils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/api/typeDocument")
 */
class TypeDocumentController extends AbstractController
{
    /**
     * @Rest\Get(path="/", name="type_document_index")
     * @Rest\View(StatusCode = 200)
     * @IsGranted("ROLE_TypeDocument_INDEX")
     */
    public function index(): array
    {
        $typeDocuments = $this->getDoctrine()
            ->getRepository(TypeDocument::class)
            ->findAll();

        return count($typeDocuments)?$typeDocuments:[];
    }

    /**
     * @Rest\Post(Path="/create", name="type_document_new")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_TypeDocument_CREATE")
     */
    public function create(Request $request): TypeDocument    {
        $typeDocument = new TypeDocument();
        $form = $this->createForm(TypeDocumentType::class, $typeDocument);
        $form->submit(Utils::serializeRequestContent($request));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($typeDocument);
        $entityManager->flush();

        return $typeDocument;
    }

    /**
     * @Rest\Get(path="/{id}", name="type_document_show",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_TypeDocument_SHOW")
     */
    public function show(TypeDocument $typeDocument): TypeDocument    {
        return $typeDocument;
    }

    
    /**
     * @Rest\Put(path="/{id}/edit", name="type_document_edit",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_TypeDocument_EDIT")
     */
    public function edit(Request $request, TypeDocument $typeDocument): TypeDocument    {
        $form = $this->createForm(TypeDocumentType::class, $typeDocument);
        $form->submit(Utils::serializeRequestContent($request));

        $this->getDoctrine()->getManager()->flush();

        return $typeDocument;
    }
    
    /**
     * @Rest\Put(path="/{id}/clone", name="type_document_clone",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_TypeDocument_CLONE")
     */
    public function cloner(Request $request, TypeDocument $typeDocument):  TypeDocument {
        $em=$this->getDoctrine()->getManager();
        $typeDocumentNew=new TypeDocument();
        $form = $this->createForm(TypeDocumentType::class, $typeDocumentNew);
        $form->submit(Utils::serializeRequestContent($request));
        $em->persist($typeDocumentNew);

        $em->flush();

        return $typeDocumentNew;
    }

    /**
     * @Rest\Delete("/{id}", name="type_document_delete",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_TypeDocument_DELETE")
     */
    public function delete(TypeDocument $typeDocument): TypeDocument    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($typeDocument);
        $entityManager->flush();

        return $typeDocument;
    }
    
    /**
     * @Rest\Post("/delete-selection/", name="type_document_selection_delete")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_TypeDocument_DELETE")
     */
    public function deleteMultiple(Request $request): array {
        $entityManager = $this->getDoctrine()->getManager();
        $typeDocuments = Utils::getObjectFromRequest($request);
        if (!count($typeDocuments)) {
            throw $this->createNotFoundException("Selectionner au minimum un élément à supprimer.");
        }
        foreach ($typeDocuments as $typeDocument) {
            $typeDocument = $entityManager->getRepository(TypeDocument::class)->find($typeDocument->id);
            $entityManager->remove($typeDocument);
        }
        $entityManager->flush();

        return $typeDocuments;
    }
}
