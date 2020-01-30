<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Utils;

use App\Entity\Tracelog;
use JMS\Serializer\SerializerBuilder as Serializer;
use Symfony\Component\HttpFoundation\Request;

/**
 * Description of Utils
 *
 * @author bambo
 */
class Utils
{

    static $senderName = 'SICOFT - Université de Thiès';
    static $senderEmail = 'noreply@univ-thies.sn';
    static $siteUrl = 'http://localhost:4200';

    public static function serializeRequestContent(Request $request)
    {
        return json_decode($request->getContent(), true);
    }

    public static function getObjectFromRequest(Request $request)
    {
        return json_decode($request->getContent());
    }
    
    public static function create($em, $ressource, $operation, $oldValue, $newValue, $user_email): void
    {
        $tracelog = new Tracelog();
        $tracelog
            ->setDate(new \DateTime())
            ->setNewvalue(Utils::serialize($newValue))
            ->setOperation($operation)
            ->setOldvalue(Utils::serialize($oldValue))
            ->setRessource($ressource)
            ->setUserEmail($user_email);

        $em->persist($tracelog);
        $em->flush();
    }

    public static function serialize($object)
    {
        $serializer = Serializer::create()->build();
        return $serializer->serialize($object, 'json');
    }

}
