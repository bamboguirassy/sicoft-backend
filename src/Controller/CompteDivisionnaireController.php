<?php

namespace App\Controller;

use App\Entity\CompteDivisionnaire;
use App\Entity\SousClasse;
use App\Form\CompteDivisionnaireType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Utils\Utils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/api/compteDivisionnaire")
 */
class CompteDivisionnaireController extends AbstractController
{
    /**
     * @Rest\Get(path="/", name="compte_divisionnaire_index")
     * @Rest\View(StatusCode = 200)
     * @IsGranted("ROLE_CompteDivisionnaire_INDEX")
     */
    public function index(): array
    {
        $compteDivisionnaires = $this->getDoctrine()
            ->getRepository(CompteDivisionnaire::class)
            ->findAll();

        return count($compteDivisionnaires) ? $compteDivisionnaires : [];
    }

    /**
     * @Rest\Post(Path="/create", name="compte_divisionnaire_new")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_CompteDivisionnaire_CREATE")
     */
    public function create(Request $request): CompteDivisionnaire
    {
        $compteDivisionnaire = new CompteDivisionnaire();
        $form = $this->createForm(CompteDivisionnaireType::class, $compteDivisionnaire);
        $form->submit(Utils::serializeRequestContent($request));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($compteDivisionnaire);
        $entityManager->flush();

        return $compteDivisionnaire;
    }


    /**
     * @Rest\Post(path="/create-multiple", name="compte_divisionnaire_create_multiple")
     * @Rest\View(statusCode=200)
     * @IsGranted("ROLE_CompteDivisionnaire_CREATE")
     */
    public function createMultiple(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $compteDivs = Utils::serializeRequestContent($request);

        $createdItems = [];

        foreach ($compteDivs as $compteDiv) {
            $divisionalAccount = new CompteDivisionnaire();
            $form = $this->createForm(CompteDivisionnaireType::class, $divisionalAccount);
            $form->submit($compteDiv);

            $searchedDivisionalAccount = $em->getRepository(CompteDivisionnaire::class)
                ->findOneBy(['numero' => $divisionalAccount->getNumero()]);
            if ($searchedDivisionalAccount) {
                throw $this->createAccessDeniedException("Ce code est déjà celui d'un compte divisionnaire.");
            }

            $em->persist($divisionalAccount);
            $createdItems[] = $divisionalAccount;
        }

        $em->flush();
        return $createdItems;


    }

    /**
     * @Rest\Get(path="/{id}", name="compte_divisionnaire_show",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_CompteDivisionnaire_SHOW")
     */
    public function show(CompteDivisionnaire $compteDivisionnaire): CompteDivisionnaire
    {
        return $compteDivisionnaire;
    }


    /**
     * @Rest\Put(path="/{id}/edit", name="compte_divisionnaire_edit",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_CompteDivisionnaire_EDIT")
     */
    public function edit(Request $request, CompteDivisionnaire $compteDivisionnaire): CompteDivisionnaire
    {
        $form = $this->createForm(CompteDivisionnaireType::class, $compteDivisionnaire);
        $form->submit(Utils::serializeRequestContent($request));

        $this->getDoctrine()->getManager()->flush();

        return $compteDivisionnaire;
    }

    /**
     * @Rest\Put(path="/{id}/clone", name="compte_divisionnaire_clone",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_CompteDivisionnaire_CLONE")
     */
    public function cloner(Request $request, CompteDivisionnaire $compteDivisionnaire): CompteDivisionnaire
    {
        $em = $this->getDoctrine()->getManager();
        $compteDivisionnaireNew = new CompteDivisionnaire();
        $form = $this->createForm(CompteDivisionnaireType::class, $compteDivisionnaireNew);
        $form->submit(Utils::serializeRequestContent($request));
        $em->persist($compteDivisionnaireNew);

        $em->flush();

        return $compteDivisionnaireNew;
    }

    /**
     * @Rest\Delete("/{id}", name="compte_divisionnaire_delete",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_CompteDivisionnaire_DELETE")
     */
    public function delete(CompteDivisionnaire $compteDivisionnaire): CompteDivisionnaire
    {
        if (count($compteDivisionnaire->getComptes())) {
            throw new HttpException(417, "Impossible de supprimer le compte divisionnaire");
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($compteDivisionnaire);
        $entityManager->flush();

        return $compteDivisionnaire;
    }

    /**
     * @Rest\Delete("/{id}/confirm", name="compte_divisionnaire_after_confirmation_delete",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=204)
     * @IsGranted("ROLE_CompteDivisionnaire_DELETE")
     */
    public function deleteAfterConfirmation(CompteDivisionnaire $compteDivisionnaire): CompteDivisionnaire
    {
        $comptes = $compteDivisionnaire->getComptes();
        $entityManager = $this->getDoctrine()->getManager();


        foreach ($comptes as $compte) {
            $compte->setCompteDivisionnaire(null);
            $entityManager->flush();
            $entityManager->remove($compte);
        }
        $entityManager->remove($compteDivisionnaire);
        $entityManager->flush();

        return $compteDivisionnaire;
    }

    /**
     * @Rest\Post("/delete-selection/", name="compte_divisionnaire_selection_delete")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_CompteDivisionnaire_DELETE")
     */
    public function deleteMultiple(Request $request): array
    {
        $entityManager = $this->getDoctrine()->getManager();
        $compteDivisionnaires = Utils::getObjectFromRequest($request);
        if (!count($compteDivisionnaires)) {
            throw $this->createNotFoundException("Selectionner au minimum un élément à supprimer.");
        }
        foreach ($compteDivisionnaires as $compteDivisionnaire) {
            $compteDivisionnaire = $entityManager->getRepository(CompteDivisionnaire::class)->find($compteDivisionnaire->id);
            $entityManager->remove($compteDivisionnaire);
        }
        $entityManager->flush();

        return $compteDivisionnaires;
    }

    /**
     * @Rest\Get(path="/{id}/sous-classe", name="sous_classe")
     * @Rest\View(StatusCode = 200)
     * @IsGranted("ROLE_CompteDivisionnaire_INDEX")
     */
    public function findBySousClasse(SousClasse $sousClasse)
    {
        $sousClasses = $this->getDoctrine()
            ->getRepository(CompteDivisionnaire::class)
            ->findBySousClasse($sousClasse);

        return count($sousClasses) ? $sousClasses : [];
    }
}
