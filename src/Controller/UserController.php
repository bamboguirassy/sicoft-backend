<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use JMS\Serializer\SerializerInterface;
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
class UserController extends AbstractController {

    /**
     * @Rest\Get(path="/", name="user_index")
     * @Rest\View(StatusCode = 200)
     * @IsGranted("ROLE_User_INDEX")
     */
    public function index(): array {
        $users = $this->getDoctrine()
                ->getRepository(User::class)
                ->findAll();

        return count($users) ? $users : [];
    }

    /**
     * @Rest\Post(Path="/create", name="user_new")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_User_CREATE")
     */
    public function create(Request $request, \Swift_Mailer $mailer, \Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface $passwordEncoder): User {
        $entityManager = $this->getDoctrine()->getManager();
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->submit(Utils::serializeRequestContent($request));
        //check if email already exist
        $searchedUserByEmail = $entityManager->getRepository(User::class)
                ->findByEmail($user->getEmail());
        if (count($searchedUserByEmail)) {
            throw $this->createAccessDeniedException("Cette adresse email est déja utilisée pour un autre compte...");
        }
        $user->setUsername($user->getEmail());
        $confirmationToken = md5(random_bytes(20));
        $user->setConfirmationToken($confirmationToken);
        $user->setPasswordRequestedAt(new \DateTime());
        $user->setPassword($passwordEncoder->encodePassword($user, 'bienvenue'));
        $user->setEnabled(false);
        $entityManager->persist($user);
        $entityManager->flush();

        //send confirmation mail
        $message = (new \Swift_Message('Creation Compte SICOFT'))
                ->setFrom(\App\Utils\Utils::$senderEmail)
                ->setTo($user->getEmail())
                ->setBody(
                $this->renderView(
                        'emails/register.html.twig', ['user' => $user, 'siteUrl' => \App\Utils\Utils::$siteUrl.'/reset-password/'.$confirmationToken]
                ), 'text/html'
        );
        $mailer->send($message);

        return $user;
    }

    /**
     * @Rest\Get(path="/{id}", name="user_show",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_User_SHOW")
     */
    public function show(User $user): User {
        return $user;
    }

    /**
     * @Rest\Put(path="/{id}/edit", name="user_edit",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_User_EDIT")
     */
    public function edit(Request $request, User $user): User {
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
    public function cloner(Request $request, User $user, \Swift_Mailer $mailer, \Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface $passwordEncoder): User {
        $entityManager = $this->getDoctrine()->getManager();
        $userNew = new User();
        $form = $this->createForm(UserType::class, $userNew);
        $form->submit(Utils::serializeRequestContent($request));
        //check if email already exist
        $searchedUserByEmail = $entityManager->getRepository(User::class)
                ->findByEmail($userNew->getEmail());
        if (count($searchedUserByEmail)) {
            throw $this->createAccessDeniedException("Cette adresse email est déja utilisée pour un autre compte...");
        }
        $userNew->setUsername($userNew->getEmail());
        $plainPassword= md5(random_bytes(10));
        $userNew->setPassword($passwordEncoder->encodePassword($userNew, $plainPassword));
        $entityManager->persist($userNew);
        $entityManager->flush();

        //send confirmation mail
        $message = (new \Swift_Message('Creation Compte SICOFT'))
                ->setFrom(\App\Utils\Utils::$senderEmail)
                ->setTo($userNew->getEmail())
                ->setBody(
                $this->renderView(
                        'emails/register.html.twig', ['user' => $userNew, 'siteUrl' => \App\Utils\Utils::$siteUrl,'password'=>$plainPassword]
                ), 'text/html'
        );
        $mailer->send($message);

        return $userNew;
    }

    /**
     * @Rest\Delete("/{id}", name="user_delete",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_User_DELETE")
     */
    public function delete(User $user): User {
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

    /**
     * @param $term
     * @Rest\Get(path="/search/")
     * @Rest\QueryParam(
     *     name="term",
     *     nullable=true,
     *     description="Le terme a rechercher"
     * )
     * @Rest\QueryParam(
     *     name="sortOrder",
     *     nullable=true,
     *     requirements="asc|desc",
     *     default="asc",
     *     description="Ordre d'affichage des user"
     * )
     * @return mixed
     */

    public function findUserByTerm($term, $sortOrder, UserRepository $userRepository, SerializerInterface $serializer) {

        $users = $userRepository->searchByTerm($term, $sortOrder);
        $serializedUsers = $serializer->serialize($users, 'json') ;
        return new Response($serializedUsers);
    }

}
