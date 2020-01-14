<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Utils\Utils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/api/user")
 */
class UserController extends AbstractController
{
    /**
     * @Rest\Get(path="/", name="user_index")
     * @Rest\View(StatusCode = 200)
     * @IsGranted("ROLE_User_INDEX")
     */
    public function index(): array
    {
        $users = $this->getDoctrine()
            ->getRepository(User::class)
            ->findAll();

        return count($users)?$users:[];
    }

    /**
     * @Rest\Post(Path="/create", name="user_new")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_User_CREATE")
     */
    public function create(Request $request): User    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->submit(Utils::serializeRequestContent($request));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $user;
    }

    /**
     * @Rest\Get(path="/{id}", name="user_show",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_User_SHOW")
     */
    public function show(User $user): User    {
        return $user;
    }

    
    /**
     * @Rest\Put(path="/{id}/edit", name="user_edit",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_User_EDIT")
     */
    public function edit(Request $request, User $user): User    {
        $form = $this->createForm(UserType::class, $user);
        $form->submit(Utils::serializeRequestContent($request));

        $this->getDoctrine()->getManager()->flush();

        return $user;
    }
    
    /**
     * @Rest\Put(path="/{id}/clone", name="user_clone",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_User_CLONE")
     */
    public function cloner(Request $request, User $user):  User {
        $em=$this->getDoctrine()->getManager();
        $userNew=new User();
        $form = $this->createForm(UserType::class, $userNew);
        $form->submit(Utils::serializeRequestContent($request));
        $em->persist($userNew);

        $em->flush();

        return $userNew;
    }

    /**
     * @Rest\Delete("/{id}", name="user_delete",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_User_DELETE")
     */
    public function delete(User $user): User    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($user);
        $entityManager->flush();

        return $user;
    }
    
    /**
     * @Rest\Post("/delete-selection/", name="user_selection_delete")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_User_DELETE")
     */
    public function deleteMultiple(Request $request): array {
        $entityManager = $this->getDoctrine()->getManager();
        $users = Utils::getObjectFromRequest($request);
        if (!count($users)) {
            throw $this->createNotFoundException("Selectionner au minimum un élément à supprimer.");
        }
        foreach ($users as $user) {
            $user = $entityManager->getRepository(User::class)->find($user->id);
            $entityManager->remove($user);
        }
        $entityManager->flush();

        return $users;
    }
}
