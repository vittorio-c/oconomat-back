<?php

namespace App\Repository;

use App\Entity\Recipe;
use App\Entity\Ingredient;
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

    /**
     * Get global average price of all recipes
     *
     * @return array
     */
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

    /**
     * Get all recipes
     */
    public function getAllRecipes()
    {
        $em = $this->getEntityManager();
        $RAW_SQL = '
            SELECT *
            FROM recipe r
            ';
        $statement = $em->getConnection()->prepare($RAW_SQL);
        $statement->execute();

        return $statement->fetchAll();

    }

    /**
     * Get all recipes but the ones with animal products
     */
    public function getVegetarianRecipes()
    {
        $em = $this->getEntityManager();
        $RAW_SQL = '
            SELECT *
            FROM recipe r
            WHERE NOT EXISTS
              (SELECT food.type
                FROM ingredient 
                INNER JOIN food on food.id = ingredient.aliment_id
                WHERE ingredient.recipe_id = r.id AND 
                  food.type IN (\'viande\', \'fruit de mer\',\'poisson\')
              )
            ';
        $statement = $em->getConnection()->prepare($RAW_SQL);
        $statement->execute();

        return $statement->fetchAll();
    }
}
