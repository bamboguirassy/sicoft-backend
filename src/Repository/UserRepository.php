<?php


namespace App\Repository;


use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function searchByTerm($term, $sortOrder = 'asc') {
        $term = strtolower($term);
        $sortOrder = strtolower($sortOrder);

        $queryBuilder = $this->createQueryBuilder('u')
            ->select('u')
            ->orderBy('u.prenom', $sortOrder);

        if($term) {
            $queryBuilder
                ->where('u.prenom LIKE :prenom')
                ->orWhere('u.email LIKE :email')
                ->orWhere('u.nom LIKE :nom')
                ->setParameter('prenom', '%'.$term.'%')
                ->setParameter('email', '%'.$term.'%')
                ->setParameter('nom', '%'.$term.'%');
        }


        return $queryBuilder->getQuery()->getResult();
    }

}