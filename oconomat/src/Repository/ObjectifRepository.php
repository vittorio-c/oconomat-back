<?php

namespace App\Repository;

use App\Entity\Objectif;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Objectif|null find($id, $lockMode = null, $lockVersion = null)
 * @method Objectif|null findOneBy(array $criteria, array $orderBy = null)
 * @method Objectif[]    findAll()
 * @method Objectif[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ObjectifRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Objectif::class);
    }

/*
SELECT recipe.title AS recipeTitle, recipe.id AS recipeId, ingredient.id AS ingredientId, ingredient.quantity AS quantity, food.name AS foodName, food.price AS foodPrice, (food.price*ingredient.quantity) AS ingredientPrice
FROM `recipe`
INNER JOIN ingredient ON recipe.id = ingredient.recipe_id
INNER JOIN food ON ingredient.aliment_id = food.id
WHERE recipe.id = 2

SELECT SUM((food.price*ingredient.quantity)) AS totalPrice
FROM `recipe`
INNER JOIN ingredient ON recipe.id = ingredient.recipe_id
INNER JOIN food ON ingredient.aliment_id = food.id
WHERE recipe.id = 2

 */

    // /**
    //  * @return Objectif[] Returns an array of Objectif objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
     */

    /*
    public function findOneBySomeField($value): ?Objectif
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
     */

    public function getLastBudget($userId)
    {
        return $this->createQueryBuilder('o')
        ->andWhere('o.user = :val')
        ->orderBy('o.createdAt', 'DESC')
        ->setParameter('val', $userId)
        ->getQuery()
        ->getResult()
    ;  
    }
}
