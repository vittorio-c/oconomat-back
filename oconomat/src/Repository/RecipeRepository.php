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
        /*$em = $this->getEntityManager();
        $query = $em->createQuery(
            '
            SELECT r 
            FROM App\Entity\Recipe r
            WHERE r.id NOT IN (
            SELECT
               re.id
            FROM App\Entity\Recipe re
            INNER JOIN App\Entity\Ingredient i 
            INNER JOIN App\Entity\Food  f
            WHERE f.type IN (\'viande\', \'fruit de mer\', \'poisson\')
            )
            ');

        $result = $query->getResult();
         */
        //  azdazd
        /*$sub = $this->createQueryBuilder('r');
        $subQuery = $sub->select('r')
                        ->innerJoin('r.ingredients', 'i')
                        ->innerJoin('i.aliment', 'f')
                        ->where('f.type IN (:param)')
                        ->setParameter('param', array('viande', 'fruit de mer', 'poisson'))
                        ->getQuery()
                        ->getResult();

        $qb = $this->createQueryBuilder('r');
        $query = $qb->select('r')
                    ->where($qb->expr()->notIn('r.id', $subQuery))
                    ->getQuery()
                    ->getResult();
        return $query;*/

/*
        $qb = $this->createQueryBuilder('r');
        $query = $qb->select('r')
                    ->andWhere(
                        $qb->expr()->not(
                            $qb->expr()->exists($sub->getDQL())
                        )
                    )
                    ->getQuery();

        $recipes = $query->execute();
return $recipes;*/
    }
}
/*
        $em = $this->getEntityManager();
        $query = $em->createQuery(
            'SELECT r.id
            FROM App\Entity\Recipe r
            WHERE NOT EXISTS
            (SELECT f.type
            FROM App\Entity\Ingredient i
            INNER JOIN App\Entity\Food f
            WHERE i.id = r.id 
            AND f.type IN (\'viande\'))
            ORDER BY r.id');

        $result = $query->getResult();
 */
        /*$sub = $this->createQueryBuilder('r');
        $sub->select('r');
        $sub->from('App\Entity\Recipe', 'r');
        //$sub->where('i.id = 1514');
        $sub->innerJoin('r.ingredient', 'i');
        $sub->innerJoin('i.aliment', 'f');
        $sub->where('f.type IN (:param)');
        $sub->setParameter('param', array('viande', 'fruit de mer', 'poisson'));*/

        /*$qb = $this->createQueryBuilder('r');
        $query = $qb->select('r')
                    ->innerJoin('r.ingredients', 'i')
                    ->innerJoin('i.aliment', 'f')
                    ->where($qb->expr()->notIn('f.type', ':param'))
                    ->setParameter('param', array('viande', 'fruit de mer', 'poisson'))
                    ->getQuery();
        $recipes = $query->execute();
        return $recipes;*/

/*
 * ceci me retourne que les recettes avec viande :
 $qb = $this->createQueryBuilder('r');
$query = $qb->select('r')
            ->innerJoin('r.ingredients', 'i')
            ->innerJoin('i.aliment', 'f')
            ->where('f.type IN (:param)')
            ->setParameter('param', array('viande', 'fruit de mer', 'poisson'))
            ->getQuery();
$recipes = $query->execute();
return $recipes;

 */
