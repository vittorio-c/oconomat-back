<?php

namespace App\Repository;

use App\Entity\Food;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Food|null find($id, $lockMode = null, $lockVersion = null)
 * @method Food|null findOneBy(array $criteria, array $orderBy = null)
 * @method Food[]    findAll()
 * @method Food[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FoodRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Food::class);
    }

    public function findTypes()
    {
        $qb = $this->createQueryBuilder('f');
        $query = $qb->select('f.type AS types')
                    ->groupBy('types')
                    ->getQuery();
        $types = $query->execute();
        return $types;
    }

    public function findUnits()
    {
        $qb = $this->createQueryBuilder('f');
        $query = $qb->select('f.unit AS units')
                    ->groupBy('units')
                    ->getQuery();
        $units = $query->execute();
        return $units;
    }
}
