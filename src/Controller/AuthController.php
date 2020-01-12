<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Description of AuthController
 *
 * @author bambo
 *
 * @Route("/api/auth")
 */
class AuthController extends AbstractController {

    /**
     * @Rest\Get(path="/current_user/", name="current_user_show")
     * @Rest\View(StatusCode=200)
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function getCurrentUser(): User {
        return $this->getUser();
    }

}
