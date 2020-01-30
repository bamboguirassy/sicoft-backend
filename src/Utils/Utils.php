<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Utils;

use App\Entity\Tracelog;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

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
        if ($object != null) {
            $encoders = new JsonEncoder();
            $normalizers = new ObjectNormalizer();
            $normalizer->setCircularReferenceHandler(function ($object) {
                return $object->getId(); // Change this to a valid method of your object
            });

            $serializer = new Serializer(array($normalizers), array($encoders));

            return $serializer->serialize($object, 'json');
        }
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
        $em->manager->flush();
    }

}
