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
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

/**
 * @Route("/api/user")
 */
class UserController extends AbstractController
{
    private $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

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

        return count($users) ? $users : [];
    }

    /**
     * @Rest\Post(Path="/create", name="user_new")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_User_CREATE")
     */
    public function create(Request $request, \Swift_Mailer $mailer, \Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface $passwordEncoder): User
    {
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
        //verification numéro de téléphone unique
        $searchedUserBytelephone = $entityManager->getRepository(User::class)
            ->findByTelephone($user->getTelephone());
        if (count($searchedUserBytelephone)) {
            throw $this->createAccessDeniedException("Cette numéro est déja utilisée pour un autre compte...");
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
                    'emails/register.html.twig', ['user' => $user, 'siteUrl' => \App\Utils\Utils::$siteUrl . '/reset-password/' . $confirmationToken]
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
    public function show(User $user): User
    {
        return $user;
    }

    /**
     * @Rest\Put(path="/{id}/edit", name="user_edit",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_User_EDIT")
     */
    public function edit(Request $request, User $user): User
    {
        $form = $this->createForm(UserType::class, $user);
        $form->submit(Utils::serializeRequestContent($request));

        $searchedUser = $this->getDoctrine()->getManager()
            ->createQuery(
                'SELECT u FROM App\Entity\User u
                 WHERE (u.email=:email OR u.telephone=:phone) AND u!=:user
            ')->setParameter('phone', $user->getTelephone())
        ->setParameter('email', $user->getEmail())
        ->setParameter('user', $user)->getResult();
        if ($searchedUser) {
            if ($searchedUser[0]->getEmail() == $user->getEmail()) {
                throw $this->createAccessDeniedException("Cette adresse e-mail existe déjà.");
            }

            if ($searchedUser[0]->getTelephone() == $user->getTelephone()) {
                throw  $this->createAccessDeniedException("Ce numéro telephone existe déjà.");
            }
        }

        $this->getDoctrine()->getManager()->flush();

        return $user;
    }

    /**
     * @Rest\Put(path="/password_update", name="password_update")
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_User_EDIT")
     */
    public function updatePassword(Request $request, \Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface $passwordEncoder)
    {
        $em = $this->getDoctrine()->getManager();
        $requestData = utils::getObjectFromRequest($request);
        $verification = password_verify($requestData->currentPassword, $this->getUser()->getPassword());
        if (!$verification) {
            throw $this->createAccessDeniedException("Votre mot de passe actuel est incorrect");
        }
        if ($requestData->newPassword != $requestData->confirmPassword) {
            throw $this->createAccessDeniedException("Le nouveau mot de passe saisi ne correcpond pas au mot de passe de confirmation");
        }
        $this->getUser()->setPassword($passwordEncoder->encodePassword($this->getUser(), $requestData->newPassword));
        $em->flush();
    }

    /**
     * @Rest\Put(path="/{id}/clone", name="user_clone",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_User_CLONE")
     */
    public function cloner(Request $request, User $user, \Swift_Mailer $mailer, \Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface $passwordEncoder): User
    {
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
        //verification numéro de téléphone unique
        $searchedProviderByTelephone = $entityManager->getRepository(User::class)
            ->findOneByTelephone($userNew->getTelephone());
        if ($searchedProviderByTelephone) {
            throw $this->createAccessDeniedException("Un utilisateur avec ce même numéro existe déjà.");
        }
        $userNew->setUsername($userNew->getEmail());
        $plainPassword = md5(random_bytes(10));
        $userNew->setPassword($passwordEncoder->encodePassword($userNew, $plainPassword));
        $userNew->setConfirmationToken( md5(random_bytes(20)));
        $userNew->setPasswordRequestedAt(new \DateTime());
        $entityManager->persist($userNew);
        $entityManager->flush();

        //send confirmation mail
        $message = (new \Swift_Message('Creation Compte SICOFT'))
            ->setFrom(\App\Utils\Utils::$senderEmail)
            ->setTo($userNew->getEmail())
            ->setBody(
                $this->renderView(
                    'emails/register.html.twig', ['user' => $userNew, 'siteUrl' => \App\Utils\Utils::$siteUrl, 'password' => $plainPassword]
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
    public function delete(User $user): User
    {
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
    public function deleteMultiple(Request $request): array
    {
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
     * @Rest\Put(path="/{id}/edit_profil", name="edit_profil",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_User_EDIT")
     */
    public function editProfil(Request $request, User $user): User
    {
        $form = $this->createForm(UserType::class, $user);
        $form->submit(Utils::serializeRequestContent($request));

        $targetUser = $this->getDoctrine()->getManager()
            ->createQuery(
                'SELECT user FROM App\Entity\User user
                 WHERE (user.prenom=:prenom OR user.nom=:nom OR user.email=:email OR user.telephone=:telephone OR user.fonction=:fonction) AND user!=:user
            ')->setParameter('prenom', $user->getPrenom())
            ->setParameter('nom', $user->getNom())
            ->setParameter('telephone', $user->getTelephone())
            ->setParameter('fonction', $user->getFonction())
            ->setParameter('email', $user->getEmail())
            ->setParameter('user', $user)
            ->getResult();
        if (count($targetUser)) {
            if ($targetUser[0]->getTelephone() == $user->getTelephone()) {
                throw $this->createAccessDeniedException("Ce numéro de telephone existe déjà.");
            }
        }
        $this->getDoctrine()->getManager()->flush();
        return $user;

    }

    /**
     * @Rest\Put(path="/change_image_profil", name="changeImage",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     * @IsGranted("ROLE_User_EDIT")
     */
    public function uploadFileProfil(Request $request) {
        /** @var UploadedFile $file */
        $file = $request->files->get('image');
        $fileName = $request->request->get('filename');
        $contentFile = base64_decode($file);
        return $file;  

        $fileSystem = new \Symfony\Component\Filesystem\Filesystem();
        $em = $this->getDoctrine()->getManager();
        $path = $this->params->get('photo_files_directory') . '/' . $this->getUser()->getPhotoUrl();

        try {
            if ($fileSystem->exists($path)) {
                $fileSystem->remove($path);
            }
        } catch (IOExceptionInterface $exception) {
            echo "An error occurred while deleting the file at" . $exception->getPath();
        }
        //manage new file upload
        
        $file = NULL;
            $file = $request->files->get('image');
            return $file;
            
        if (!$file){
            throw $this->createAccessDeniedException('Aucun fichier trouvé');
        }
        if ($file) {
            $fileName = $this->getUser()->getPassword().'.'.$file->guessExtension();
            // moves the file to the directory where brochures are stored
            $file->move(
                $this->params->get('photo_files_directory'), $fileName
            );
            $this->getUser()->setPhotoUrl($fileName);
            $em->flush();
        }
        return $file;
    }

}
