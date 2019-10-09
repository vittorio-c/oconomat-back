<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Recipe;

class MenuGenerator
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em; 
    }

    /**
     * Create a new menu
     *
     * @return array $menu
     *
     */
    public function generateMenu($budget, $quantity)
    {
        // prix objectif par recette
        $targetPrice = round($budget / $quantity);

        $lunchs = $this->em->getRepository(Recipe::class)
                       ->findBy(['type' => 'dejeuner']);
        $dinners = $this->em->getRepository(Recipe::class)
                        ->findBy(['type' => 'diner']);

        // on écrase les tableaux précédents avec des tableaux 
        // contenant uniquement des recettes correspondant plus ou moins au prix objectif
        $lunchs = $this->getTargetedRecipesWithPrice($lunchs, $targetPrice);
        $dinners = $this->getTargetedRecipesWithPrice($dinners, $targetPrice);

        // construction du menu
        $menus = $this->buildMenu($lunchs, $dinners, $quantity);

        if ($menus === false) {
            return false;
        }

        // calcul du prix total du menu
        $total = $this->getMenuTotalPrice($menus['menu']);

        // ajustements pour être sur de ne pas dépasser le prix
        $menu = $this->adjustMenu($menus['menu'], $menus['menuLeft'], $budget);

        return $menu;
    }

    /**
     * Correct generated menu's price
     * to make it the closest posible 
     * to user's budget
     *
     * @return array $menu
     *
     */
    public function adjustMenu($menu, $left, $budget)
    {
        while ($this->getMenuTotalPrice($menu) > $budget) {
            // NB max et min retournent la valeur de l'élément trouvé, et non l'index
            // il faut donc chercher ensuite avec array_search pour avoir l'id de la recette

            // get id of most expensive recipe from $menu
            $max = max(array_column($menu, 'price'));

            $maxId = array_keys(array_filter(
                $menu, 
                function ($item) use ($max) {
                    return $item['price'] === $max;
                }
            ))[0]; 

            $maxType = $menu[$maxId]['type'];

            // s'assurer que l'item ajouté sera du même type que l'item enlevé
            $leftFilterdByType = array_filter(
                $left,
                function ($item) use ($maxType) {
                    return $item['type'] === $maxType;
                }
            );

            if (empty($leftFilterdByType)) {
                return false;
            }

            $min = min(array_column($leftFilterdByType, 'price'));

            $minId = array_keys(array_filter(
                $leftFilterdByType,
                function ($item) use ($min) {
                    return $item['price'] === $min;
                }
            ))[0];

            // enlever le plus chere du menu
            unset($menu[$maxId]);
            // ajouter le moins cher au menu
            $menu[$minId] = $left[$minId];
            // enlever le moins cher du $left pour 
            // être certain de ne pas retomber dessus à la prochaine boucle
            unset($left[$minId]);
        }

        return $menu;
    }

    /**
     * Get menu total price
     *
     * @return int $total
     *
     */
    public function getMenuTotalPrice($menu)
    {
        $total = 0;
        foreach ($menu as $id => $value) {
            $total += $value['price'];
        }
        return $total;
    }

    /**
     * Get recipes price
     *
     * @return array $recipesArray
     *
     */
    public function getTargetedRecipesWithPrice(array $recipes, $targetPrice)
    {
        $repository = $this->em->getRepository(Recipe::class);

        // tableau avec id de la  recette et prix total
        $recipesArray = [];
        foreach ($recipes as $recipe) {
            $price = $repository->getRecipieTotalPrice($recipe->getId()); 
            $diff = $price[0]['totalPrice'] - $targetPrice;

            if ($diff <= 5 && $diff >= -5) {
                $recipesArray[$recipe->getId()] = [
                    'price' => intval($price[0]['totalPrice']),
                    'type' => $recipe->getType()
                ];
            }
        }
        return $recipesArray;
    }

    /**
     * Build menu 
     *
     * TODO lever une exception plutôt que le return false
     * une fois que la gestion des exception sera en place
     *
     * @return array [$menus, $menusLeft]
     *
     */
    public function buildMenu($lunchs, $dinners, $quantity)
    {
        // tableau menu avec les 21 recettes sélectionnées
        $menu = [];
        // tableau avec les recettes non sélectionnées
        $menuLeft = [];
        $allRecipes = $lunchs + $dinners;
        $quantity = round($quantity / 2);

        if (count($lunchs) < $quantity || count($dinners) < $quantity) {
            return false;
        }

        // tableau numérique temporaire ([numKey => recipeId]) pour pouvoir tirer au hasard
        // une recette sur la base du nombre généré plus bas
        $arrayLunchs = array_keys($lunchs);
        $arrayDinners = array_keys($dinners);

        // 7 lunchs
        for ($i = 0; $i < $quantity; $i++) {
            $n = rand(0, count($lunchs) - 1);
            $key = $arrayLunchs[$n];
            $value = $lunchs[$key];
            if (!array_key_exists($key, $menu)) {
                $menu[$key] = $value;
            } else {
                // quant dans $menu il a déjà ajouté toutes les valeurs 
                // présentes dans $arrayLunchs
                // sortir de la boucle et lever une erreure
                $i--;
            }
        }

        // 7 dinners
        for ($i = 0; $i < $quantity; $i++) {
            $n = rand(0, count($dinners) - 1);
            $key = $arrayDinners[$n];
            $value = $dinners[$key];
            if (!array_key_exists($key, $menu)) {
                $menu[$key] = $value;
            } else {
                $i--;
            }
        }

        // built menu "left" for futures purposes
        foreach ($allRecipes as $key => $value) {
            if (!array_key_exists($key, $menu)) {
                $menuLeft[$key] = $value;
            }
        }

        if (count($menuLeft) < $quantity) {
            return false;
        }

        return ['menu' => $menu, 'menuLeft' => $menuLeft];
    }
}

