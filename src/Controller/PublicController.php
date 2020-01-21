<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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
 * Description of PublicController
 *
 * @author Moussa FOFANA
 * @Route("/api/public")
 */
class PublicController extends AbstractController {

    /**
     * @Rest\Post(path="/change-password/{id}", name="change_password")
     * @Rest\View(StatusCode=200)
     */
    public function changePassword(Request $request, User $user, \Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface $passwordEncoder) {
        $password = $request->getContent();
        $em = $this->getDoctrine()->getManager();
        $user->setPassword($passwordEncoder->encodePassword($user, $password));
        if (!$user->isEnabled()) {
            $user->setEnabled(true);
            $user->setPasswordRequestedAt(null);
            $user->setConfirmationToken(null);
        }
        $em->flush();
    }

    /**
     * @Rest\Post(path="/verificate-token/", name="verificate_token")
     * @Rest\View(StatusCode=200)
     */
    public function verificateToken(Request $request) {
        $token = $request->getContent();
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository(User::class)->findByConfirmationToken($token);
        if (!count($users)) {
            throw $this->createNotFoundException("Token Introuvable");
        }
        $user = $users[0];

        if ($user->isEnabled()) {
            //si compte actif, simple changement de mot de passe
            //durée token : 2 jours
            $interval = date_diff($user->getPasswordRequestedAt(), new \DateTime(), false);
            if ($interval->format('%R%a') > '+2') {
                $user->setPasswordRequestedAt(null);
                $user->setConfirmationToken(null);
                throw $this->createAccessDeniedException("Ce lien a expiré");
            }
        }

        $em->flush();

        return $user;
    }

    /**
     * @Rest\Post(path="/ask-reset-password/", name="ask_reset_password")
     * @Rest\View(StatusCode=200)
     */
    public function askResetPassword(Request $request, \Swift_Mailer $mailer) {
        $email = $request->getContent();
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneByEmail($email);
        if(!$user){
            throw $this->createNotFoundException("Cette adresse email n'est pas reconnue...");
        }
        $confirmationToken = md5(random_bytes(20));
        $user->setConfirmationToken($confirmationToken);
        $user->setPasswordRequestedAt(new \DateTime());
        $em->flush();

        //send confirmation mail
        $message = (new \Swift_Message('Réinitialisation Compte SICOFT'))
                ->setFrom(\App\Utils\Utils::$senderEmail)
                ->setTo($user->getEmail())
                ->setBody(
                $this->renderView(
                        'emails/reset-password.html.twig', ['user' => $user, 'siteUrl' => \App\Utils\Utils::$siteUrl . '/reset-password/' . $confirmationToken]
                ), 'text/html'
        );
        $mailer->send($message);

        return $user;
    }

}
