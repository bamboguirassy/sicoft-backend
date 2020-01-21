<?php

namespace App\Controller;

use App\Entity\Entite;
use App\Form\EntiteType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Utils\Utils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/api/entite")
 */
class EntiteController extends AbstractController
{
    /**
     * @Rest\Get(path="/", name="entite_index")
     * @Rest\View(StatusCode = 200)
     * @IsGranted("ROLE_Entite_INDEX")
     */
    public function index(): array
    {
        $entites = $this->getDoctrine()
            ->getRepository(Entite::class)
            ->findAll();

        return count($entites)?$entites:[];
    }

    /**
     * @Rest\Post(Path="/create", name="entite_new")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Entite_CREATE")
     */
    public function create(Request $request): Entite    {
        $entite = new Entite();
        $form = $this->createForm(EntiteType::class, $entite);
        $form->submit(Utils::serializeRequestContent($request));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($entite);
        $entityManager->flush();

        return $entite;
    }

    /**
     * @Rest\Get(path="/{id}", name="entite_show",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Entite_SHOW")
     */
    public function show(Entite $entite): Entite    {
        return $entite;
    }

    
    /**
     * @Rest\Put(path="/{id}/edit", name="entite_edit",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Entite_EDIT")
     */
    public function edit(Request $request, Entite $entite): Entite    {
        $form = $this->createForm(EntiteType::class, $entite);
        $form->submit(Utils::serializeRequestContent($request));

        $this->getDoctrine()->getManager()->flush();

        return $entite;
    }
    
    /**
     * @Rest\Put(path="/{id}/clone", name="entite_clone",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Entite_CLONE")
     */
    public function cloner(Request $request, Entite $entite):  Entite {
        $em=$this->getDoctrine()->getManager();
        $entiteNew=new Entite();
        $form = $this->createForm(EntiteType::class, $entiteNew);
        $form->submit(Utils::serializeRequestContent($request));
        $em->persist($entiteNew);

        $em->flush();

        return $entiteNew;
    }

    /**
     * @Rest\Delete("/{id}", name="entite_delete",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Entite_DELETE")
     */
    public function delete(Entite $entite): Entite    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($entite);
        $entityManager->flush();

        return $entite;
    }
    
    /**
     * @Rest\Post("/delete-selection/", name="entite_selection_delete")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Entite_DELETE")
     */
    public function deleteMultiple(Request $request): array {
        $entityManager = $this->getDoctrine()->getManager();
        $entites = Utils::getObjectFromRequest($request);
        if (!count($entites)) {
            throw $this->createNotFoundException("Selectionner au minimum un élément à supprimer.");
        }
        foreach ($entites as $entite) {
            $entite = $entityManager->getRepository(Entite::class)->find($entite->id);
            $entityManager->remove($entite);
        }
        $entityManager->flush();

        return $entites;
    }
}
