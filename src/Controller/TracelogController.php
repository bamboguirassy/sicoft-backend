<?php

namespace App\Controller;

use App\Entity\Tracelog;
use App\Utils\Utils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use Doctrine\ORM\EntityManagerInterface as Manager;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/tracelog")
 */
class TracelogController extends AbstractController
{
    /**
     * @var Manager
     */
    private $manager;

    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @Rest\Get("/", name="tracelog_index")
     * @Rest\View(StatusCode = 200)
     */
    public function index(): array
    {
        $tracelogs = $this->manager
            ->getRepository(Tracelog::class)
            ->findAll();
        return count($tracelogs) ? $tracelogs : [];
    }

    /**
     * @Rest\Get("/{id}", name="tracelog_show")
     * @Rest\View(statusCode=200)
     */
    public function show(Tracelog $tracelog): Tracelog
    {
        return $tracelog;
    }

    /**
     * @Rest\Delete("/{id}", name="tracelog_delete")
     * @Rest\View(statusCode=200)
     */
    public function delete(Tracelog $tracelog): Tracelog
    {
        $this->manager->remove($tracelog);
        $this->manager->flush();

        return $tracelog;
    }

    // remove given tracelogs from database
    public function deleteTracelogs($tracelogs): array
    {
        if (!count($tracelogs)) {
            throw $this->createNotFoundException("Sélectionner au minimum un élément à supprimer.");
        }
        foreach ($tracelogs as $tracelog) {
            $tracelog = $this->manager->getRepository(Tracelog::class)->find($tracelog->id);
            $this->manager->remove($tracelog);
        }
        $this->manager->flush();

        return $tracelogs;
    }

    /**
     * @Rest\Post(path="/delete-selection", name="tracelog_selection_delete")
     * @Rest\View(statusCode=200)
     */
    public function deleteMultiple(Request $request): array
    {
        $tracelogs = Utils::getObjectFromRequest($request);
        return $this->deleteTracelogs($tracelogs);
    }

    /**
     * @Rest\Post(path="/delete-all", name="tracelog_selection_delete_all")
     * @Rest\View(statusCode=200)
     */
    public function emptyTracelogs(): array
    {
        return $this->deleteTracelogs($this->index());
    }
}
