<?php

namespace App\Controller;

use App\Entity\CategorieClasse;
use App\Form\CategorieClasseType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Utils\Utils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/api/categorieClasse")
 */
class CategorieClasseController extends AbstractController
{
    /**
     * @Rest\Get(path="/", name="categorie_classe_index")
     * @Rest\View(StatusCode = 200)
     * @IsGranted("ROLE_CategorieClasse_INDEX")
     */
    public function index(): array
    {
        $categorieClasses = $this->getDoctrine()
            ->getRepository(CategorieClasse::class)
            ->findAll();

        return count($categorieClasses)?$categorieClasses:[];
    }

    /**
     * @Rest\Post(Path="/create", name="categorie_classe_new")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_CategorieClasse_CREATE")
     */
    public function create(Request $request): CategorieClasse    {
        $categorieClasse = new CategorieClasse();
        $form = $this->createForm(CategorieClasseType::class, $categorieClasse);
        $form->submit(Utils::serializeRequestContent($request));
        $entityManager = $this->getDoctrine()->getManager();

        $searchedProviderByCode= $entityManager->getRepository(CategorieClasse::class)
            ->findOneByCode($categorieClasse->getCode());
        if ($searchedProviderByCode) {
            throw $this->createAccessDeniedException("Une Catégorie classe avec ce même code existe déjà.");
        }


        $searchedProviderByNom= $entityManager->getRepository(CategorieClasse::class)
            ->findOneByNom($categorieClasse->getNom());
        if ($searchedProviderByNom) {
            throw $this->createAccessDeniedException("Une Catégorie Classe avec ce même nom existe déjà.");
        }
        $entityManager->persist($categorieClasse);
        $entityManager->flush();

        return $categorieClasse;
    }

    /**
     * @Rest\Get(path="/{id}", name="categorie_classe_show",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_CategorieClasse_SHOW")
     */
    public function show(CategorieClasse $categorieClasse): CategorieClasse    {
        return $categorieClasse;
    }

    
    /**
     * @Rest\Put(path="/{id}/edit", name="categorie_classe_edit",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_CategorieClasse_EDIT")
     */
    public function edit(Request $request, CategorieClasse $categorieClasse): CategorieClasse    {
        $form = $this->createForm(CategorieClasseType::class, $categorieClasse);
        $form->submit(Utils::serializeRequestContent($request));

        $targetFournisseur = $this->getDoctrine()->getManager()
            ->createQuery(
                'SELECT categorieClasse FROM App\Entity\CategorieClasse categorieClasse
                 WHERE (categorieClasse.nom=:nom OR categorieClasse.code=:code) AND categorieClasse!=:categorieClasse
            ')->setParameter('code', $categorieClasse->getCode())
            ->setParameter('nom', $categorieClasse->getNom())
            ->setParameter('categorieClasse', $categorieClasse)
            ->getResult();
        if($targetFournisseur) {
            if ($targetFournisseur[0]->getCode() == $categorieClasse->getCode()) {
                throw $this->createAccessDeniedException("Une Catégorie Classe avec ce même code existe déjà.");
            }

            if($targetFournisseur[0]->getNom() == $categorieClasse->getNom()) {
                throw $this->createAccessDeniedException("Une Catégorie Classe avec ce même nom existe déjà.");
            }


        }

        $this->getDoctrine()->getManager()->flush();

        return $categorieClasse;
    }
    
    /**
     * @Rest\Put(path="/{id}/clone", name="categorie_classe_clone",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_CategorieClasse_CLONE")
     */
    public function cloner(Request $request, CategorieClasse $categorieClasse):  CategorieClasse {
        $em=$this->getDoctrine()->getManager();
        $categorieClasseNew=new CategorieClasse();
        $form = $this->createForm(CategorieClasseType::class, $categorieClasseNew);
        $form->submit(Utils::serializeRequestContent($request));


        $searchedProviderByCode = $em->getRepository(CategorieClasse::class)
            ->findOneByCode($categorieClasseNew->getCode());
        if ($searchedProviderByCode) {
            throw $this->createAccessDeniedException("Une Catégorie Classe avec ce même code existe déjà.");
        }

        $searchedProviderByNom = $em->getRepository(CategorieClasse::class)
            ->findOneByNom($categorieClasseNew->getNom());
        if ($searchedProviderByNom) {
            throw $this->createAccessDeniedException("Une Catégorie Classe avec ce même nom existe déjà.");
        }

        $em->persist($categorieClasseNew);
        $em->flush();

        return $categorieClasseNew;
    }

    /**
     * @Rest\Delete("/{id}", name="categorie_classe_delete",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_CategorieClasse_DELETE")
     */
    public function delete(CategorieClasse $categorieClasse): CategorieClasse    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($categorieClasse);
        $entityManager->flush();

        return $categorieClasse;
    }
    
    /**
     * @Rest\Post("/delete-selection/", name="categorie_classe_selection_delete")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_CategorieClasse_DELETE")
     */
    public function deleteMultiple(Request $request): array {
        $entityManager = $this->getDoctrine()->getManager();
        $categorieClasses = Utils::getObjectFromRequest($request);
        if (!count($categorieClasses)) {
            throw $this->createNotFoundException("Selectionner au minimum un élément à supprimer.");
        }
        foreach ($categorieClasses as $categorieClasse) {
            $categorieClasse = $entityManager->getRepository(CategorieClasse::class)->find($categorieClasse->id);
            $entityManager->remove($categorieClasse);
        }
        $entityManager->flush();

        return $categorieClasses;
    }
}
