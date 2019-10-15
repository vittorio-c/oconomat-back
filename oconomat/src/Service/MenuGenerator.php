<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Recipe;

class MenuGenerator
{
    public const QUANTITY = 21;
    private $em;
    private $users;
    private $budget;
    private $targetPrice;
    private $vegetarian;

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
    public function generateMenu($budget, $users, $vegetarian)
    {
        $this->budget = $budget;
        $this->users = $users;
        $this->vegetarian = $vegetarian;
        $recipeRepo = $this->em->getRepository(Recipe::class);

        // prix objectif par recette
        $this->targetPrice = round($this->budget / self::QUANTITY, 2);
        $averagePriceFromDB = floatval($recipeRepo->getAllRecipiesAveragePrice()[0]['average']) * $this->users;

        $total = floatval($recipeRepo->getTotal()[0]['total']);

        $variation = ($this->targetPrice - $averagePriceFromDB) / $averagePriceFromDB * 100;

        // si le prix cible dépasse de 50 % à la baisse ou à la hausse 
        // la moyenne des prix en database
        if ($variation < -50 || $variation > 50) {
            return false;
        }

        $lunchs = [];
        $dinners = [];
        $breakfast = [];

        if ($vegetarian) {
            $recipes = $recipeRepo->getVegetarianRecipes();
        } else {
            $recipes = $recipeRepo->getAllRecipes();
        }

        foreach ($recipes as $recipe) {
            switch ($recipe['type']) {
            case 'petit déjeuner':
                $breakfast[] = $recipe; 
                break;
            case 'déjeuner':
                $lunchs[] = $recipe;
                break;
            case 'dîner':
                $dinners[] = $recipe;
                break;
            }
        }

        // on écrase les tableaux précédents avec des tableaux 
        // contenant uniquement des recettes correspondant plus ou moins au prix objectif
        $lunchs = $this->getTargetedRecipesWithPrice($lunchs);
        $dinners = $this->getTargetedRecipesWithPrice($dinners);
        $breakfast = $this->getTargetedRecipesWithPrice($breakfast);
        //dump($breakfast, $lunchs, $dinners); exit;

        // construction du menu
        $menus = $this->buildMenu($breakfast, $lunchs, $dinners);

        // calcul du prix total du menu
        $total = $this->getMenuTotalPrice($menus['menu']);

        // ajustements pour être sur de ne pas dépasser le prix
        $menu = $this->adjustMenu($menus['menu'], $menus['menuLeft']);

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
    public function adjustMenu($menu, $left)
    {
        while ($this->getMenuTotalPrice($menu) > $this->budget) {

            $arrayLeft = array_column($left, 'price');

            // le minimum disponible dans price
            $min = min($arrayLeft);
            $minId = array_keys(array_filter(
                $left,
                function ($item) use ($min) {
                    return $item['price'] === $min;
                }
            ))[0];
            $minType = $left[$minId]['type'];

            // s'assurer que l'item enlevé sera du même type que l'item ajouté
            $menuByType = array_filter(
                $menu,
                function ($item) use ($minType) {
                    return $item['type'] === $minType;
                }
            );

            // le maximum dans menu
            $max = max(array_column($menuByType, 'price'));
            $maxId = array_keys(array_filter(
                $menuByType, 
                function ($item) use ($max) {
                    return $item['price'] === $max;
                }
            ))[0]; 

            // On fait l'échange
            unset($menu[$maxId]);
            $menu[$minId] = $left[$minId];
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
     * Filter recipes on target price
     * Return an array like : [recipeId => [price => 2, type => dejeuner]]
     *
     * @return array $recipesArray
     *
     */
    public function getTargetedRecipesWithPrice(array $recipes)
    {
        //dump($recipes); exit;
        $repository = $this->em->getRepository(Recipe::class);

        // tableau avec id de la  recette et prix total
        $recipesArray = [];

        // on élargit la limite pour les petits dejueners
        // car il y en a peu pour le moment, et ils sont tres peu chers
        if ($recipes[0]['type'] == 'petit déjeuner') {
            $limit = 150;
        } else {
            $limit = 75;
        }

        foreach ($recipes as $recipe) {
            $price = round(
                $repository->getRecipieTotalPrice(
                    $recipe['id'])[0]['totalPrice'], 
                    2) * $this->users; 
            $variation = ($price - ($this->targetPrice)) / ($this->targetPrice) * 100;

            if ($variation > $limit || $variation < -$limit) {
            } else {
                $recipesArray[$recipe['id']] = [
                    'price' => $price,
                    'type' => $recipe['type']
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
    public function buildMenu($breakfast, $lunchs, $dinners)
    {
        // tableau menu avec les 21 recettes sélectionnées
        $menu = [];
        // tableau avec les recettes non sélectionnées
        $menuLeft = [];

        $allRecipes = $breakfast + $lunchs + $dinners;

        $quantity = self::QUANTITY / 3;

        // gestion d'un budget trop grand :
        // si le nombre de possibilités est plus faible que la quantité demandée
        // rappel : le nombre de possibilités est ici déjà filtré par le prix objectif
        // autrement dit : si trop peu ou pas de repas correspondent au prix objectif
        // alors exit
        //if (count($lunchs) < $quantity || count($dinners) < $quantity || count($breakfast) < $quantity) {
        //return false;
        //}

        // tableau numérique temporaire ([numKey => recipeId]) pour pouvoir tirer au hasard
        // une recette sur la base du nombre généré plus bas
        $arrayBreakfast = array_keys($breakfast);
        $arrayLunchs = array_keys($lunchs);
        $arrayDinners = array_keys($dinners);

        // 7 breakfast
        for ($i = 0; $i < $quantity; $i++) {
            $n = rand(0, count($breakfast) - 1);
            $key = $arrayBreakfast[$n];
            $value = $breakfast[$key];
            if (!array_key_exists($key, $menu)) {
                $menu[$key] = $value;
            } else {
                $i--;
            }
        }

        // 7 lunchs
        for ($i = 0; $i < $quantity; $i++) {
            $n = rand(0, count($lunchs) - 1);
            $key = $arrayLunchs[$n];
            $value = $lunchs[$key];
            if (!array_key_exists($key, $menu)) {
                $menu[$key] = $value;
            } else {
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

        return ['menu' => $menu, 'menuLeft' => $menuLeft];
    }
}

