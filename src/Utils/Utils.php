<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Utils;

use App\Entity\Tracelog;
use Doctrine\Common\Persistence\ObjectManager;
use JMS\Serializer\SerializerBuilder as Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

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

    public static function serialize($object)
    {
        $serializer = Serializer::create()->build();
        return $serializer->serialize($object, 'json');
    }
    
    public static function createTracelog($manager, $ressource, $operation, $oldValue, $newValue, $user_email): void
    {
        if(isset($oldValue)) $oldValue = Utils::serialize($oldValue);
        if(isset($newValue)) $newValue = Utils::serialize($newValue);

        $tracelog = new Tracelog();
        $tracelog->setDate(new \DateTime())->setNewvalue($newValue)->setOperation($operation)->setOldvalue($oldValue)->setRessource($ressource)->setUserEmail($user_email);
        $manager->persist($tracelog);
        $manager->flush();
    }
    
}
