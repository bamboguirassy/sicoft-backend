<?php

namespace App\Controller;

use App\Entity\Budget;
use App\Entity\Exercice;
use App\Form\BudgetType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Utils\Utils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/api/budget")
 */
class BudgetController extends AbstractController
{
    /**
     * @Rest\Get(path="/", name="budget_index")
     * @Rest\View(StatusCode = 200)
     * @IsGranted("ROLE_Budget_INDEX")
     */
    public function index(): array
    {
        $budgets = $this->getDoctrine()
            ->getRepository(Budget::class)
            ->findAll();

        return count($budgets)?$budgets:[];
    }

    /**
     * @Rest\Get(path="/entite/access", name="budget_by_entite_access")
     * @Rest\View(StatusCode = 200)
     * @IsGranted("ROLE_Budget_INDEX")
     */
    public function findBudgetByEntiteAccess() {
        $em = $this->getDoctrine()->getManager();
        $budgetsByEntites = [];
        $groupes = $this->getUser()->getGroups();
        foreach ($groupes as $groupe) {
        if ($groupe->getCode() == 'SA') {
            $budgetsByEntites = $em->createQuery('SELECT bgt FROM App\Entity\Budget bgt')
            ->getResult();
            return count($budgetsByEntites)?$budgetsByEntites:[];
            } 
        }
        $entites = $this->getUser()->getEntites();
      
        foreach($entites as $entite){
            $tabBudgets = $em->createQuery('SELECT bgt FROM App\Entity\Budget bgt WHERE bgt.entite=?1')
            ->setParameter(1, $entite->getId())
            ->getResult();
            $budgetsByEntites = array_merge($budgetsByEntites, $tabBudgets);
        }

        return count($budgetsByEntites)?$budgetsByEntites:[];
    }

    /**
     * @Rest\Post(Path="/create", name="budget_new")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Budget_CREATE")
     */
    public function create(Request $request): Budget  {
        $entityManager = $this->getDoctrine()->getManager();
        $budget = new Budget();
        $form = $this->createForm(BudgetType::class, $budget);
        $form->submit(Utils::serializeRequestContent($request));
        
       $existBudget = $entityManager->createQuery('SELECT bgt FROM App\Entity\Budget bgt
        WHERE bgt.exercice=?1 OR bgt.entite=?2' )
        ->setParameter(1, $budget->getExercice())
        ->setParameter(2, $budget->getEntite())
        ->getResult();
        if(count($existBudget) > 0){
            throw $this->createNotFoundException("Cet exercice est dèjà rattaché à une entité!");
        }    
        $budget->setLibelle('Exercice' . ' ' .$budget->getExercice()->getLibelle(). ' ' .$budget->getEntite()->getCode());
        
        $entityManager = $this->getDoctrine()->getManager();
        
        $entityManager->persist($budget);
        $entityManager->flush();

        return $budget;
    }

    /**
     * @Rest\Get(path="/{id}", name="budget_show",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Budget_SHOW")
     */
    public function show(Budget $budget): Budget    {
        return $budget;
    }

    
    /**
     * @Rest\Put(path="/{id}/edit", name="budget_edit",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Budget_EDIT")
     */
    public function edit(Request $request, Budget $budget): Budget    {
        $entityManager = $this->getDoctrine()->getManager();
        $form = $this->createForm(BudgetType::class, $budget);
        $form->submit(Utils::serializeRequestContent($request));

        $existBudget = $entityManager->createQuery('SELECT bgt FROM App\Entity\Budget bgt
        WHERE bgt.exercice=?1 and bgt.entite=?2 and bgt!=?3')
        ->setParameter(1, $budget->getExercice())
        ->setParameter(2, $budget->getEntite())
        ->setParameter(3, $budget)
        ->getResult();
        if(count($existBudget) > 0){
            throw $this->createNotFoundException("Cet exercice est dèjà rattaché à une entité!");
        }
        
        $this->getDoctrine()->getManager()->flush();

        return $budget;
    }
    
    /**
     * @Rest\Put(path="/{id}/clone", name="budget_clone",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Budget_CLONE")
     */
    public function cloner(Request $request, Budget $budget):  Budget {
        $em=$this->getDoctrine()->getManager();
        $budgetNew=new Budget();
        $form = $this->createForm(BudgetType::class, $budgetNew);
        $form->submit(Utils::serializeRequestContent($request));

        $existBudget = $em->createQuery('SELECT bgt FROM App\Entity\Budget bgt
        WHERE bgt.exercice=?1 and bgt.entite=?2')
        ->setParameter(1, $budget->getExercice())
        ->setParameter(2, $budget->getEntite())
        ->getResult();
        if(count($existBudget) > 0){
            throw $this->createNotFoundException("Cet exercice est dèjà rattaché à une entité!");
        }

        $em->persist($budgetNew);

        $em->flush();

        return $budgetNew;
    }

    /**
     * @Rest\Delete("/{id}", name="budget_delete",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Budget_DELETE")
     */
    public function delete(Budget $budget): Budget    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($budget);
        $entityManager->flush();

        return $budget;
    }
    
    /**
     * @Rest\Post("/delete-selection/", name="budget_selection_delete")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Budget_DELETE")
     */
    public function deleteMultiple(Request $request): array {
        $entityManager = $this->getDoctrine()->getManager();
        $budgets = Utils::getObjectFromRequest($request);
        if (!count($budgets)) {
            throw $this->createNotFoundException("Selectionner au minimum un élément à supprimer.");
        }
        foreach ($budgets as $budget) {
            $budget = $entityManager->getRepository(Budget::class)->find($budget->id);
            $entityManager->remove($budget);
        }
        $entityManager->flush();

        return $budgets;
    }
}
