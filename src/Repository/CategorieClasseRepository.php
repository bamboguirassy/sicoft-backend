<?php

namespace App\Repository;

use App\Entity\CategorieClasse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method CategorieClasse|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategorieClasse|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategorieClasse[]    findAll()
 * @method CategorieClasse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategorieClasseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategorieClasse::class);
    }



}
