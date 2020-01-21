<?php

namespace App\Controller;

use App\Entity\RoleSurMarche;
use App\Form\RoleSurMarcheType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Utils\Utils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/api/roleSurMarche")
 */
class RoleSurMarcheController extends AbstractController
{
    /**
     * @Rest\Get(path="/", name="role_sur_marche_index")
     * @Rest\View(StatusCode = 200)
     * @IsGranted("ROLE_RoleSurMarche_INDEX")
     */
    public function index(): array
    {
        $roleSurMarches = $this->getDoctrine()
            ->getRepository(RoleSurMarche::class)
            ->findAll();

        return count($roleSurMarches)?$roleSurMarches:[];
    }

    /**
     * @Rest\Post(Path="/create", name="role_sur_marche_new")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_RoleSurMarche_CREATE")
     */
    public function create(Request $request): RoleSurMarche    {
        $roleSurMarche = new RoleSurMarche();
        $form = $this->createForm(RoleSurMarcheType::class, $roleSurMarche);
        $form->submit(Utils::serializeRequestContent($request));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($roleSurMarche);
        $entityManager->flush();

        return $roleSurMarche;
    }

    /**
     * @Rest\Get(path="/{id}", name="role_sur_marche_show",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_RoleSurMarche_SHOW")
     */
    public function show(RoleSurMarche $roleSurMarche): RoleSurMarche    {
        return $roleSurMarche;
    }

    
    /**
     * @Rest\Put(path="/{id}/edit", name="role_sur_marche_edit",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_RoleSurMarche_EDIT")
     */
    public function edit(Request $request, RoleSurMarche $roleSurMarche): RoleSurMarche    {
        $form = $this->createForm(RoleSurMarcheType::class, $roleSurMarche);
        $form->submit(Utils::serializeRequestContent($request));

        $this->getDoctrine()->getManager()->flush();

        return $roleSurMarche;
    }
    
    /**
     * @Rest\Put(path="/{id}/clone", name="role_sur_marche_clone",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_RoleSurMarche_CLONE")
     */
    public function cloner(Request $request, RoleSurMarche $roleSurMarche):  RoleSurMarche {
        $em=$this->getDoctrine()->getManager();
        $roleSurMarcheNew=new RoleSurMarche();
        $form = $this->createForm(RoleSurMarcheType::class, $roleSurMarcheNew);
        $form->submit(Utils::serializeRequestContent($request));
        $em->persist($roleSurMarcheNew);

        $em->flush();

        return $roleSurMarcheNew;
    }

    /**
     * @Rest\Delete("/{id}", name="role_sur_marche_delete",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_RoleSurMarche_DELETE")
     */
    public function delete(RoleSurMarche $roleSurMarche): RoleSurMarche    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($roleSurMarche);
        $entityManager->flush();

        return $roleSurMarche;
    }
    
    /**
     * @Rest\Post("/delete-selection/", name="role_sur_marche_selection_delete")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_RoleSurMarche_DELETE")
     */
    public function deleteMultiple(Request $request): array {
        $entityManager = $this->getDoctrine()->getManager();
        $roleSurMarches = Utils::getObjectFromRequest($request);
        if (!count($roleSurMarches)) {
            throw $this->createNotFoundException("Selectionner au minimum un élément à supprimer.");
        }
        foreach ($roleSurMarches as $roleSurMarche) {
            $roleSurMarche = $entityManager->getRepository(RoleSurMarche::class)->find($roleSurMarche->id);
            $entityManager->remove($roleSurMarche);
        }
        $entityManager->flush();

        return $roleSurMarches;
    }
}
