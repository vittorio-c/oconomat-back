<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Menu;

/**
 * Generate shopping list from $menu
 */
class ShoppingList
{
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function generate($menu)
    {
        // get shopping list
        $data = $this->em->getRepository(Menu::class)
                   ->getShoppinigListFromMenuId($menu->getId());

        // merge duplicate items and get shopping list total price
        $newData = [];
        $shoppingTotalPrice = 0;

        foreach ($data as $key => $value) {
            $quantity = round(floatval($value['quantity']), 2);
            $totalPrice = round(floatval($value['totalPrice']), 2);
            $value['quantity'] = $quantity;
            $value['totalPrice'] = $totalPrice;

            $arrayTemp = array_column($newData, 'foodId');

            if (!in_array($value['foodId'], $arrayTemp)) {
                $newData[] = $value;
            } else {
                $id = array_search($value['foodId'], $arrayTemp);
                $newData[$id]['quantity'] += $quantity;
                $newData[$id]['totalPrice'] += $totalPrice;
            }
            $shoppingTotalPrice += $totalPrice;
        }
        $data = $newData;

        // construct shopping list's metadatas
        $metadata = [
            'menuId' => $menu->getId(),
            'createdAt' => $menu->getCreatedAt(),
            'userId' => $menu->getUser()->getId(),
            'userQuantity' => $menu->getObjectif()->getUserQuantity(),
            'shoppingTotalPrice' => $shoppingTotalPrice
        ];

        // prepare php array
        $shoppingList = [];
        $shoppingList['metadata'] = $metadata;
        $shoppingList['shoppingList'] = $data;

        return $shoppingList;
    }
}

