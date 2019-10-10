<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Recipe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Recipe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Recipe[]    findAll()
 * @method Recipe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipe::class);
    }

    public function getRecipieTotalPrice($recipeId)
    {
        $qb = $this->createQueryBuilder('r');
        $query = $qb->select('sum((a.price*i.quantity)) AS totalPrice')
                    ->innerJoin('r.ingredients', 'i')
                    ->innerJoin('i.aliment', 'a')
                    ->where('r.id = ?1')
                    ->setParameter(1, $recipeId)
                    ->getQuery();
        $menu = $query->execute();
        return $menu;
    }

    public function getAllRecipiesAveragePrice()
    {
        $em = $this->getEntityManager();
        $RAW_SQL = '
            SELECT 
                ROUND(AVG(totalPrice), 2) average
            FROM
                (SELECT 
                        SUM((food.price*ingredient.quantity)) as totalPrice,
                        recipe.id
                FROM recipe
                INNER JOIN ingredient ON ingredient.recipe_id = recipe.id
                INNER JOIN food ON food.id = ingredient.aliment_id
                GROUP BY recipe.id) 
            prices;
            ';
        $statement = $em->getConnection()->prepare($RAW_SQL);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function getTotal()
    {
        $em = $this->getEntityManager();
        $RAW_SQL = '
            SELECT 
                ROUND(SUM(totalPrice), 2) total
            FROM
                (SELECT 
                        SUM((food.price*ingredient.quantity)) as totalPrice,
                        recipe.id
                FROM recipe
                INNER JOIN ingredient ON ingredient.recipe_id = recipe.id
                INNER JOIN food ON food.id = ingredient.aliment_id
                GROUP BY recipe.id) 
            prices;
            ';
        $statement = $em->getConnection()->prepare($RAW_SQL);
        $statement->execute();

        return $statement->fetchAll();
    }
}
