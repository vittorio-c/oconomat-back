<?php

namespace App\Repository;

use App\Entity\Menu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Menu|null find($id, $lockMode = null, $lockVersion = null)
 * @method Menu|null findOneBy(array $criteria, array $orderBy = null)
 * @method Menu[]    findAll()
 * @method Menu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MenuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Menu::class);
    }

    public function getShoppinigListFromMenuId($menuId) 
    {
        $qb = $this->createQueryBuilder('m');
        $query = $qb->select('a.id AS foodId', 'a.name', 'i.quantity', 'a.price', 'a.unit', '(a.price*i.quantity) AS totalPrice')
                    ->innerJoin('m.recipes', 'r')
                    ->innerJoin('r.ingredients', 'i')
                    ->innerJoin('i.aliment', 'a')
                    ->where('m.id = ?1')
                    ->setParameter(1, $menuId)
                    ->orderBy('foodId', 'ASC')
                    ->getQuery();
        $menu = $query->execute();
        return $menu;
/*
SELECT recipe_menu.menu_id,  menu.created_at, recipe_menu.recipe_id, ingredient.aliment_id, food.name, food.price, food.unit, ingredient.quantity,(food.price*ingredient.quantity) AS total_price
FROM `menu`
INNER JOIN recipe_menu ON menu.id = recipe_menu.menu_id
INNER JOIN ingredient ON recipe_menu.recipe_id = ingredient.recipe_id
INNER JOIN food ON ingredient.aliment_id = food.id
WHERE menu.id = 1

'm.id AS menuId', 'm.createdAt', 'r.id AS recipeId', 
 */
    }

    // /**
    //  * @return Menu[] Returns an array of Menu objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
     */

    /*
    public function findOneBySomeField($value): ?Menu
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
     */
}
