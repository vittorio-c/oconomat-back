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
        $query = $qb->select(
            'a.id AS foodId',
            'a.name',
            '(i.quantity*o.userQuantity) AS quantity',
            'a.price',
            'a.unit',
            '(a.price*i.quantity)*o.userQuantity AS totalPrice')
                    ->innerJoin('m.recipes', 'r')
                    ->innerJoin('m.objectif', 'o')
                    ->innerJoin('r.ingredients', 'i')
                    ->innerJoin('i.aliment', 'a')
                    ->where('m.id = ?1')
                    ->setParameter(1, $menuId)
                    ->orderBy('foodId', 'ASC')
                    ->getQuery();
        $menu = $query->execute();
        return $menu;
    }
}
