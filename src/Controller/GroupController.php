<?php

namespace App\Controller;

use App\Entity\Group;
use App\Form\GroupType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Utils\Utils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/api/group")
 */
class GroupController extends AbstractController {

    /**
     * @Rest\Get(path="/", name="group_index")
     * @Rest\View(StatusCode = 200)
     * @IsGranted("ROLE_Group_INDEX")
     */
    public function index(): array {
        $groups = $this->getDoctrine()
                ->getRepository(Group::class)
                ->findAll();

        return count($groups) ? $groups : [];
    }

    /**
     * @Rest\Post(Path="/create", name="group_new")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Group_CREATE")
     */
    public function create(Request $request): Group {
        $entityManager = $this->getDoctrine()->getManager();
        $group = new Group();
        $form = $this->createForm(GroupType::class, $group);
        $form->submit(Utils::serializeRequestContent($request));
        //check if code already exist
        $searchedGroupByCode = $entityManager->getRepository(Group::class)
                ->findByCode($group->getCode());
        if (count($searchedGroupByCode)) {
            throw $this->createAccessDeniedException("Un groupe avec le même code existe déjà, merci de changer de code...");
        }
        //check if group already exist
        $searchedGroupByName = $entityManager->getRepository(Group::class)
                ->findByName($group->getName());
        if (count($searchedGroupByName)) {
            throw $this->createAccessDeniedException("Un groupe avec le même nom existe déjà, merci de changer le nom...");
        }

        $serializedData = json_decode($request->getContent());
        if (!isset($serializedData->roles)) {
            throw $this->createNotFoundException("Les accès ne sont pas définis pour ce groupe...");
        }
        $accessGroups = $serializedData->roles;
        $roles = [];
        foreach ($accessGroups as $accessGroup) {
            foreach ($accessGroup->accessModels as $accessModel) {
                if ($accessModel->isCreateAllowed) {
                    $roles[] = 'ROLE_' . $accessModel->tableCode . '_CREATE';
                }
                if ($accessModel->isIndexAllowed) {
                    $roles[] = 'ROLE_' . $accessModel->tableCode . '_INDEX';
                }
                if ($accessModel->isShowAllowed) {
                    $roles[] = 'ROLE_' . $accessModel->tableCode . '_SHOW';
                }
                if ($accessModel->isCloneAllowed) {
                    $roles[] = 'ROLE_' . $accessModel->tableCode . '_CLONE';
                }
                if ($accessModel->isDeleteAllowed) {
                    $roles[] = 'ROLE_' . $accessModel->tableCode . '_DELETE';
                }
                if ($accessModel->isEditAllowed) {
                    $roles[] = 'ROLE_' . $accessModel->tableCode . '_EDIT';
                }
            }
        }

        $group->setRoles($roles);

        $entityManager->persist($group);
        $entityManager->flush();

        return $group;
    }

    /**
     * @Rest\Get(path="/{id}", name="group_show",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Group_SHOW")
     */
    public function show(Group $group): Group {
        $accessGroups = $this->getAccessGroups();
        foreach ($accessGroups as $accessGroup) {
            foreach ($accessGroup->accessModels as $accessModel) {
                $indexAccess = 'ROLE_' . $accessModel->tableCode . '_INDEX';
                $createAccess = 'ROLE_' . $accessModel->tableCode . '_CREATE';
                $showAccess = 'ROLE_' . $accessModel->tableCode . '_SHOW';
                $cloneAccess = 'ROLE_' . $accessModel->tableCode . '_CLONE';
                $deleteAccess = 'ROLE_' . $accessModel->tableCode . '_DELETE';
                $editAccess = 'ROLE_' . $accessModel->tableCode . '_EDIT';
                if (in_array($indexAccess, $group->getRoles())) {
                    $accessModel->isIndexAllowed = true;
                }
                if (in_array($createAccess, $group->getRoles())) {
                    $accessModel->isCreateAllowed = true;
                }
                if (in_array($showAccess, $group->getRoles())) {
                    $accessModel->isShowAllowed = true;
                }
                if (in_array($cloneAccess, $group->getRoles())) {
                    $accessModel->isCloneAllowed = true;
                }
                if (in_array($deleteAccess, $group->getRoles())) {
                    $accessModel->isDeleteAllowed = true;
                }
                if (in_array($editAccess, $group->getRoles())) {
                    $accessModel->isEditAllowed = true;
                }
            }
        }
        $group->setRoles($accessGroups);
        return $group;
    }

    /**
     * @Rest\Get(path="/access-group/", name="tables_list")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Group_CREATE")
     */
    public function getAccessGroups(): array {
        $accessGroups = [
            new AccessGroup("Paramètrage", [
                new AccessModel('User', "Utilisateur"),
                new AccessModel('Group', "Groupe d'utilisateur"),
                new AccessModel('Entite', 'Entités'),
                new AccessModel('TypeEntite', 'Type Entité'),
                new AccessModel('Exercice', 'Exercice'),
                new AccessModel('Classe', 'Classe'),
                new AccessModel('Compte', 'Compte'),
                new AccessModel('TypeDocument', 'Type Document'),
                new AccessModel('TypePassation', 'Type Passation'),
                new AccessModel('EtatMarche', 'Etat Marché'),
                new AccessModel('Secteur', 'Secteur'),
                new AccessModel('Fournisseur', 'Fournisseur'),
                new AccessModel('TypeSourceFinancement', 'Type source financement'),
                new AccessModel('SourceFinancement', 'source financement'),
                new AccessModel('TypeClasse', 'Type Classe'),
                new AccessModel('CategorieClasse', 'Catégorie Classe'),
                    ]
            ),
            new AccessGroup("Gestion des Marchés", [
                new AccessModel('Budget', 'Budget'),
                new AccessModel('Marche', 'Marché'),
                new AccessModel('Mandat', 'Mandat'),
            ])
        ];
        return $accessGroups;
    }

    /**
     * @Rest\Put(path="/{id}/edit", name="group_edit",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Group_EDIT")
     */
    public function edit(Request $request, Group $group): Group {
        $form = $this->createForm(GroupType::class, $group);
        $form->submit(Utils::serializeRequestContent($request));
        $serializedData = json_decode($request->getContent());
        if (!isset($serializedData->roles)) {
            throw $this->createNotFoundException("Les accès ne sont pas définis pour ce groupe...");
        }
        $accessGroups = $serializedData->roles;
        $roles = [];
        foreach ($accessGroups as $accessGroup) {
            foreach ($accessGroup->accessModels as $accessModel) {
                if ($accessModel->isCreateAllowed) {
                    $roles[] = 'ROLE_' . $accessModel->tableCode . '_CREATE';
                }
                if ($accessModel->isIndexAllowed) {
                    $roles[] = 'ROLE_' . $accessModel->tableCode . '_INDEX';
                }
                if ($accessModel->isShowAllowed) {
                    $roles[] = 'ROLE_' . $accessModel->tableCode . '_SHOW';
                }
                if ($accessModel->isCloneAllowed) {
                    $roles[] = 'ROLE_' . $accessModel->tableCode . '_CLONE';
                }
                if ($accessModel->isDeleteAllowed) {
                    $roles[] = 'ROLE_' . $accessModel->tableCode . '_DELETE';
                }
                if ($accessModel->isEditAllowed) {
                    $roles[] = 'ROLE_' . $accessModel->tableCode . '_EDIT';
                }
            }
        }

        $group->setRoles($roles);
        $this->getDoctrine()->getManager()->flush();

        return $group;
    }

    /**
     * @Rest\Put(path="/{id}/clone", name="group_clone",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Group_CLONE")
     */
    public function cloner(Request $request, Group $group): Group {
        $em = $this->getDoctrine()->getManager();
        $groupNew = new Group();
        $form = $this->createForm(GroupType::class, $groupNew);
        $form->submit(Utils::serializeRequestContent($request));

        //check if code already exist
        $searchedGroupByCode = $em->getRepository(Group::class)
                ->findByCode($group->getCode());
        if (count($searchedGroupByCode)) {
            throw $this->createAccessDeniedException("Un groupe avec le même code existe déjà, merci de changer de code...");
        }
        //check if group already exist
        $searchedGroupByName = $em->getRepository(Group::class)
                ->findByName($group->getName());
        if (count($searchedGroupByName)) {
            throw $this->createAccessDeniedException("Un groupe avec le même nom existe déjà, merci de changer le nom...");
        }

        $serializedData = json_decode($request->getContent());
        if (!isset($serializedData->roles)) {
            throw $this->createNotFoundException("Les accès ne sont pas définis pour ce groupe...");
        }
        $accessGroups = $serializedData->roles;
        $roles = [];
        foreach ($accessGroups as $accessGroup) {
            foreach ($accessGroup->accessModels as $accessModel) {
                if ($accessModel->isCreateAllowed) {
                    $roles[] = 'ROLE_' . $accessModel->tableCode . '_CREATE';
                }
                if ($accessModel->isIndexAllowed) {
                    $roles[] = 'ROLE_' . $accessModel->tableCode . '_INDEX';
                }
                if ($accessModel->isShowAllowed) {
                    $roles[] = 'ROLE_' . $accessModel->tableCode . '_SHOW';
                }
                if ($accessModel->isCloneAllowed) {
                    $roles[] = 'ROLE_' . $accessModel->tableCode . '_CLONE';
                }
                if ($accessModel->isDeleteAllowed) {
                    $roles[] = 'ROLE_' . $accessModel->tableCode . '_DELETE';
                }
                if ($accessModel->isEditAllowed) {
                    $roles[] = 'ROLE_' . $accessModel->tableCode . '_EDIT';
                }
            }
        }

        $groupNew->setRoles($roles);
        $em->persist($groupNew);

        $em->flush();

        return $groupNew;
    }

    /**
     * @Rest\Delete("/{id}", name="group_delete",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Group_DELETE")
     */
    public function delete(Group $group): Group {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($group);
        $entityManager->flush();

        return $group;
    }

    /**
     * @Rest\Post("/delete-selection/", name="group_selection_delete")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_Group_DELETE")
     */
    public function deleteMultiple(Request $request): array {
        $entityManager = $this->getDoctrine()->getManager();
        $groups = Utils::getObjectFromRequest($request);
        if (!count($groups)) {
            throw $this->createNotFoundException("Selectionner au minimum un élément à supprimer.");
        }
        foreach ($groups as $group) {
            $group = $entityManager->getRepository(Group::class)->find($group->id);
            $entityManager->remove($group);
        }
        $entityManager->flush();

        return $groups;
    }

}

class AccessModel {

    public $tableName;
    public $tableCode;
    public $isCreateAllowed;
    public $isEditAllowed;
    public $isIndexAllowed;
    public $isShowAllowed;
    public $isCloneAllowed;
    public $isDeleteAllowed;

    public function __construct($tableCode, $tableName) {
        $this->tableName = $tableName;
        $this->tableCode = $tableCode;
        $this->isCreateAllowed = false;
        $this->isEditAllowed = false;
        $this->isIndexAllowed = false;
        $this->isShowAllowed = false;
        $this->isCloneAllowed = false;
        $this->isDeleteAllowed = false;
    }

}

class AccessGroup {

    public $groupName;
    public $accessModels;

    public function __construct($groupName, $accessModels) {
        $this->groupName = $groupName;
        $this->accessModels = $accessModels;
    }

}
