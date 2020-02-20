<?php

namespace App\Controller;

use App\Entity\Exercice;
use App\Entity\ExerciceSourceFinancement;
use App\Form\ExerciceType;
use App\Utils\Utils;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/exercice")
 */
class ExerciceController extends AbstractController {

    /**
     * @Rest\Get(path="/", name="exercice_index")
     * @Rest\View(StatusCode = 200)
     * @IsGranted("ROLE_Exercice_INDEX")
     */
    public function index(): array {
       /* $repository = $this->getDoctrine()->getRepository(Exercice::class);*/
         $exercices = $this->getDoctrine()
          ->getRepository(Exercice::class)
                 ->findBy ([],['id' => 'desc']);

          return count($exercices) ? $exercices : [];
    }
 

    /**
     * @Rest\Get(path="/precedent/{id}", name="exercice_precedent", requirements = {"id"="\d+"})
     * @Rest\View(StatusCode = 200)
     * @IsGranted("ROLE_Exercice_SHOW")
     */
    public function findExercicePrecedent(Exercice $exercice){
        $targetExercicePrecedent = $this->getDoctrine()->getManager()
            ->createQuery(
                'SELECT exercice FROM App\Entity\Exercice exercice
                 WHERE (exercice.exerciceSuivant=:exercice) 
            ')->setParameter('exercice', $exercice)
            ->getSingleResult();
        if(!$targetExercicePrecedent) {
            throw new HttpException(404, "Cet exercice n'a pas de suivant.");
        }
        return $targetExercicePrecedent;
    }

    /**
     * @Rest\Post(Path="/create", name="exercice_new")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Exercice_CREATE")
     */
    public function create(Request $request): Exercice {
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

        $oldExcerciceByCode = $entityManager->getRepository(Exercice::class)->findOneByCode($exercice->getCode());
        if ($oldExcerciceByCode) {
            throw $this->createAccessDeniedException('Le code existe dèjà');
        }
        $oldExcerciceByLibelle = $entityManager->getRepository(Exercice::class)->findOneByLibelle($exercice->getLibelle());
        if ($oldExcerciceByLibelle) {
            throw $this->createAccessDeniedException('Le libelle existe dèjà');
        }

        $currentYear = $entityManager->getRepository(Exercice::class)
                ->findBy(['encours' => true]);
        if ($currentYear && $exercice->getEncours() === true) {
            throw new HttpException(417, "un exercice est déjà actif.");
        }

        $exercicePrecedant = $exercice->getExerciceSuivant();
        $exercice->setExerciceSuivant(null);
        $entityManager->persist($exercice);
        $entityManager->flush();

        if ($exercicePrecedant) {
            if ($exercicePrecedant->getExerciceSuivant()) {
                throw $this->createAccessDeniedException("L'excercie précédant est incorrect");
            }
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
    public function show(Exercice $exercice): Exercice {
        return $exercice;
    }
    
    /**
     * @Rest\Get(path="/{id}/precedent/", name="exercice_precedent",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Exercice_SHOW")
     */
    public function findExercicePrecedent(Exercice $exercice): Exercice {
         $exercicePrecedents=$this->getDoctrine()->getManager()
                ->createQuery('select e from App\Entity\Exercice e '
                        . 'where e.exerciceSuivant=?1')
                ->setParameter(1,$exercice)
                 ->getResult();
         if(count($exercicePrecedents)){
             return $exercicePrecedents[0];
         }
         throw $this->createNotFoundException();
    }

    /**
     * @Rest\Put(path="/{id}/edit", name="exercice_edit",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Exercice_EDIT")
     */
    public function edit(Request $request, Exercice $exercice): Exercice {

        $entityManager = $this->getDoctrine()->getManager();
        $form = $this->createForm(ExerciceType::class, $exercice);
        $form->submit(Utils::serializeRequestContent($request));
        $targetCode = $this->getDoctrine()->getManager()
                ->createQuery(
                        'SELECT exercice FROM App\Entity\Exercice exercice
                 WHERE (exercice.code=:code ) AND exercice!=:exercice
            ')->setParameter('code', $exercice->getCode())
                ->setParameter('exercice', $exercice)
                ->getResult();
        if (count($targetCode)) {
            if ($targetCode[0]->getCode() == $exercice->getCode()) {
                throw $this->createAccessDeniedException(" Un exercice avec le meme le code  existe déjà.");
            }
        }
        $targetLibelle = $this->getDoctrine()->getManager()
                ->createQuery(
                        'SELECT exercice FROM App\Entity\Exercice exercice
                 WHERE (exercice.libelle=:libelle ) AND exercice!=:exercice
            ')->setParameter('libelle', $exercice->getLibelle())
                ->setParameter('exercice', $exercice)
                ->getResult();
        if (count($targetLibelle)) {
            if ($targetLibelle [0]->getLibelle() == $exercice->getLibelle()) {
                throw $this->createAccessDeniedException("Un exercice avec le meme libelle   existe déjà.");
            }
        }

        $requestData = Utils::getObjectFromRequest($request);
        $datedebut = $requestData->dateDebut;
        $datefin = $requestData->dateFin;
        $exercice->setDateDebut(new \DateTime($datedebut));
        $exercice->setDateFin(new \DateTime($datefin));

        if ($exercice->getDateDebut() > $exercice->getDateFin()) {
            throw $this->createAccessDeniedException("La date de début d'exercie est supérieure à la date de fin");
        }
        $exerciceSuivant = $exercice->getExerciceSuivant();
        if ($exerciceSuivant === $exercice) {
            throw $this->createAccessDeniedException("L'excercie courant ne peut pas être son propre suivant");
        }
        if ($exerciceSuivant && $exerciceSuivant->getExerciceSuivant()) {
            throw $this->createAccessDeniedException("L'excercie suivant est incorrect");
            //$exercicePrecedant->setExerciceSuivant($exercice);
        }

        $currentYear = $entityManager->createQuery('SELECT ex FROM App\Entity\Exercice ex WHERE ex.encours=true AND ex!=:exercice')
                        ->setParameter('exercice', $exercice)->getResult();
        if ($currentYear && $exercice->getEncours() === true) {
            throw new HttpException(417, "un exercice est déjà actif.");
        }

        $entityManager->flush();

        return $exercice;
    }

    /**
     * @Rest\Put(path="/{id}/clone", name="exercice_clone",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Exercice_CLONE")
     */
    public function cloner(Request $request, Exercice $exercice): Exercice {
        $em = $this->getDoctrine()->getManager();
        $exerciceNew = new Exercice();
        $form = $this->createForm(ExerciceType::class, $exerciceNew);
        $form->submit(Utils::serializeRequestContent($request));

        $requestData = Utils::getObjectFromRequest($request);
        $datedebut = $requestData->dateDebut;
        $datefin = $requestData->dateFin;
        $exerciceNew->setDateDebut(new \DateTime($datedebut));
        $exerciceNew->setDateFin(new \DateTime($datefin));
        if ($exerciceNew->getDateDebut() > $exerciceNew->getDateFin()) {
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
        $entityManager = $this->getDoctrine()
                ->getManager();
        $currentYear = $entityManager->getRepository(Exercice::class)
                ->findBy(['encours' => true]);
        if ($currentYear && $exerciceNew->getEncours() === true) {
            throw new HttpException(417, "un exercice est déjà actif.");
        }


        $exercicePrecedant = $exercice->getExerciceSuivant();
        $exercice->setExerciceSuivant(null);

        $em->persist($exerciceNew);

        $em->flush();
        if ($exercicePrecedant) {
            if ($exercicePrecedant->getExerciceSuivant()) {
                throw $this->createAccessDeniedException("L'excercie précédant est incorrect");
            }
            $exercicePrecedant->setExerciceSuivant($exercice);
            $em->flush();
        }

        return $exerciceNew;
    }

    /**
     * @Rest\Delete("/{id}", name="exercice_delete",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Exercice_DELETE")
     */
    public function delete(Exercice $exercice): Exercice {
        $entityManager = $this->getDoctrine()->getManager();

        $exercicePrecedents = $this->getDoctrine()->getManager()
                ->createQuery(
                        'SELECT exercice FROM App\Entity\Exercice exercice
                 WHERE (exercice.exerciceSuivant=:exerciceSuivant) 
            ')->setParameter('exerciceSuivant', $exercice)
                ->getResult();
        if (count($exercicePrecedents)) {
            $exercicePrecedents[0]->setExerciceSuivant(null);
        }

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
            $targetExercice = $this->getDoctrine()->getManager()
                    ->createQuery(
                            'SELECT exercice FROM App\Entity\Exercice exercice
                 WHERE (exercice.exerciceSuivant=:exerciceSuivant) 
            ')->setParameter('exerciceSuivant', $exercice)
                    ->getResult();
            if ($targetExercice) {
                if ($targetExercice[0]->getExerciceSuivant() == $exercice) {
                    throw new HttpException(417, "Cet exercice est le suivant d'un autre.");
                }
            }
            $entityManager->remove($exercice);
        }
        $entityManager->flush();

        return $exercices;
    }

    /**
     * @Rest\Post(Path="/create-enable", name="exercice_new_enable")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Exercice_CREATE")
     */
    public function createAndDisableExerciceExcept(Request $request) {
        $exercice = new Exercice();
        $entityManager = $this->getDoctrine()->getManager();
        $form = $this->createForm(ExerciceType::class, $exercice);
        $form->submit(Utils::serializeRequestContent($request));

        $requestData = Utils::getObjectFromRequest($request);
        $datedebut = $requestData->dateDebut;
        $datefin = $requestData->dateFin;
        $exercice->setDateDebut(new \DateTime($datedebut));
        $exercice->setDateFin(new \DateTime($datefin));

        $entityManager->persist($exercice);
        $entityManager->flush();


        $this->getDoctrine()->getManager()
                ->createQuery('UPDATE App\Entity\Exercice ex SET ex.encours=false WHERE ex!=:exercice')
                ->setParameter('exercice', $exercice)
                ->getResult();

        return $exercice;
    }

    /**
     * @Rest\Put(Path="/create-enable/{id}", name="exercice_new_enable_update", requirements={"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Exercice_EDIT")
     */
    public function updateAndDisableExerciceExcept(Request $request, Exercice $exercice) {
        $form = $this->createForm(ExerciceType::class, $exercice);
        $form->submit(Utils::serializeRequestContent($request));

        $requestData = Utils::getObjectFromRequest($request);
        $datedebut = $requestData->dateDebut;
        $datefin = $requestData->dateFin;
        $exercice->setDateDebut(new \DateTime($datedebut));
        $exercice->setDateFin(new \DateTime($datefin));

        $this->getDoctrine()->getManager()->flush();

        $this->getDoctrine()->getManager()
                ->createQuery('UPDATE App\Entity\Exercice ex SET ex.encours=false WHERE ex!=:exercice')
                ->setParameter('exercice', $exercice)
                ->getResult();

        return $exercice;
    }

    /**
     * @Rest\Put(path="/create-enable/{id}/clone", name="exercice_clone_enable",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Exercice_CLONE")
     */
    public function cloneAndDisableExerciceExcept(Request $request, Exercice $exercice) {
        $em = $this->getDoctrine()->getManager();
        $exerciceNew = new Exercice();
        $form = $this->createForm(ExerciceType::class, $exerciceNew);
        $form->submit(Utils::serializeRequestContent($request));

        $requestData = Utils::getObjectFromRequest($request);
        $datedebut = $requestData->dateDebut;
        $datefin = $requestData->dateFin;
        $exerciceNew->setDateDebut(new \DateTime($datedebut));
        $exerciceNew->setDateFin(new \DateTime($datefin));

        $em->persist($exerciceNew);
        $em->flush();

        $this->getDoctrine()->getManager()
                ->createQuery('UPDATE App\Entity\Exercice ex SET ex.encours=false WHERE ex!=:exercice')
                ->setParameter('exercice', $exerciceNew)
                ->getResult();

        return $exerciceNew;
    }

    /* public function checkExrcice(Exercice $exercice, EntityManagerInterface $entityManager) {
      $searched = $entityManager->getRepository(Exercice::class)->findByCode($exercice->getCode());
      if (count($searched)) {
      throw $this->createAccessDeniedException("Une  avec le même code existe déjà, merci de changer de numéro...");
      } } */
}
