<?php

namespace App\Controller;

use App\Entity\Exercice;
use App\Form\ExerciceType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Utils\Utils;
use FOS\RestBundle\Decoder\JsonDecoder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Validator\Constraints\Length;

/**
 * @Route("/api/exercice")
 */
class ExerciceController extends AbstractController
{
    /**
     * @Rest\Get(path="/", name="exercice_index")
     * @Rest\View(StatusCode = 200)
     * @IsGranted("ROLE_Exercice_INDEX")
     */
    public function index(): array
    {
        $exercices = $this->getDoctrine()
            ->getRepository(Exercice::class)
            ->findAll();

        return count($exercices)?$exercices:[];
    }

    /**
     * @Rest\Post(Path="/create", name="exercice_new")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Exercice_CREATE")
     */
    public function create(Request $request): Exercice    {
        $exercice = new Exercice();
        $entityManager = $this->getDoctrine()->getManager();
        $form = $this->createForm(ExerciceType::class, $exercice);
        $form->submit(Utils::serializeRequestContent($request));

        $requestData = Utils::getObjectFromRequest($request);
        $datedebut = $requestData->dateDebut;
        $datefin = $requestData->dateFin;
        $exercice->setDateDebut(new \DateTime($datedebut));
        $exercice->setDateFin(new \DateTime($datefin));
        if ($exercice->getDateDebut() > $exercice->getDateFin()) {
            throw $this->createAccessDeniedException("La date de début d'exercie est supérieure à la date de fin");
        }

        $odlCodeExcercice = $entityManager->getRepository(Exercice::class)->findOneByCode($requestData->code);
            if ($odlCodeExcercice) {
                throw $this->createAccessDeniedException('Le code existe dèjà');
            }
        $odlLibelleExcercice = $entityManager->getRepository(Exercice::class)->findOneByLibelle($requestData->libelle);
            if ($odlLibelleExcercice) {
                throw $this->createAccessDeniedException('Le libelle existe dèjà');
            }
           
        $entityManager->flush();  
        $entityManager->persist($exercice);
        //$exercicePrecedant = $exercice->getExerciceSuivant();
        $exercices = $entityManager->getRepository(Exercice::class)->findAll();
        if (count($exercices) > 0){
            $exercicePrecedant = end($exercices);
            //var_dump($exercicePrecedant);
            $exercicePrecedant->setExerciceSuivant($exercice);
            $entityManager->flush();
        }
        
        

        return $exercice;
    }

    /**
     * @Rest\Get(path="/{id}", name="exercice_show",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Exercice_SHOW")
     */
    public function show(Exercice $exercice): Exercice    {
        return $exercice;
    }

    
    /**
     * @Rest\Put(path="/{id}/edit", name="exercice_edit",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Exercice_EDIT")
     */
    public function edit(Request $request, Exercice $exercice): Exercice    {
        $form = $this->createForm(ExerciceType::class, $exercice);
        $form->submit(Utils::serializeRequestContent($request));
        
        $requestData = Utils::getObjectFromRequest($request);
        $datedebut = $requestData->dateDebut;
        $datefin = $requestData->dateFin;
        $exercice->setDateDebut(new \DateTime($datedebut));
        $exercice->setDateFin(new \DateTime($datefin));
        if ($exercice->getDateDebut() > $exercice->getDateFin()) {
            throw $this->createAccessDeniedException("La date de début d'exercie est supérieure à la date de fin");
        }

        $this->getDoctrine()->getManager()->flush();

        return $exercice;
    }
    
    /**
     * @Rest\Put(path="/{id}/clone", name="exercice_clone",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Exercice_CLONE")
     */
    public function cloner(Request $request, Exercice $exercice):  Exercice {
        $em=$this->getDoctrine()->getManager();
        $exerciceNew=new Exercice();
        $form = $this->createForm(ExerciceType::class, $exerciceNew);
        $form->submit(Utils::serializeRequestContent($request));

        $requestData = Utils::getObjectFromRequest($request);
        $datedebut = $requestData->dateDebut;
        $datefin = $requestData->dateFin;
        $exercice->setDateDebut(new \DateTime($datedebut));
        $exercice->setDateFin(new \DateTime($datefin));
        if ($exercice->getDateDebut() > $exercice->getDateFin()) {
            throw $this->createAccessDeniedException("La date de début d'exercie est supérieure à la date de fin");
        }
        
        $odlCodeExcercice = $em->getRepository(Exercice::class)->findOneByCode($requestData->code);
            if ($odlCodeExcercice) {
                throw $this->createAccessDeniedException('Le code existe dèjà');
            }
        $odlLibelleExcercice = $em->getRepository(Exercice::class)->findOneByLibelle($requestData->libelle);
            if ($odlLibelleExcercice) {
                throw $this->createAccessDeniedException('Le libelle existe dèjà');
            }
        $em->persist($exerciceNew);

        $em->flush();

        return $exerciceNew;
    }

    /**
     * @Rest\Delete("/{id}", name="exercice_delete",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Exercice_DELETE")
     */
    public function delete(Exercice $exercice): Exercice    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($exercice);
        $entityManager->flush();

        return $exercice;
    }
    
    /**
     * @Rest\Post("/delete-selection/", name="exercice_selection_delete")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Exercice_DELETE")
     */
    public function deleteMultiple(Request $request): array {
        $entityManager = $this->getDoctrine()->getManager();
        $exercices = Utils::getObjectFromRequest($request);
        if (!count($exercices)) {
            throw $this->createNotFoundException("Selectionner au minimum un élément à supprimer.");
        }
        foreach ($exercices as $exercice) {
            $exercice = $entityManager->getRepository(Exercice::class)->find($exercice->id);
            $entityManager->remove($exercice);
        }
        $entityManager->flush();

        return $exercices;
    }
}
