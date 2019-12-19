<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Recipe;

class MenuGenerator
{
    /**
     * Number of recipe to generate
     *
     * @var int
     */
    const QUANTITY = 21;

    /**
     * EntityManager
     */
    private $em;

    /**
     * Number of users
     *
     * @var int
     */
    private $users;

    /**
     * @var float
     */
    private $budget;

    /**
     * Requested price per recipe
     *
     * @var float
     */
    private $targetPrice;

    /*
     *  global average price of all recipes in DB
     *
     *  @var float
     */
    private $averagePriceFromDB;

    /**
     * represents how much $targetPrice
     * is far from $averagePriceFromDB
     *
     * @var float
     */
    private $variation;

    /**
     * @var bool
     */
    private $vegetarian;

    /*
     * @var array
     */
    private $lunchs = [];

    /*
     * @var array
     */
    private $dinners = [];

    /*
     * @var array
     */
    private $breakfast = [];

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
        $recipeRepo = $this->em->getRepository(Recipe::class);

        // set values
        $this->budget = $budget;
        $this->users = $users;
        $this->vegetarian = $vegetarian;
        $this->targetPrice = round($this->budget / self::QUANTITY, 2);
        $this->averagePriceFromDB = floatval(
            $recipeRepo->getAllRecipiesAveragePrice()[0]['average']
        ) * $this->users;
        $this->variation = ($this->targetPrice - $this->averagePriceFromDB)
            / $this->averagePriceFromDB * 100;

        // if variation is too wide, abord
        if ($this->variation < -50 || $this->variation > 50) {
            // TODO rise exception instead 
            return false;
        }

        // vegetarian filter 
        if ($vegetarian === true) {
            $recipes = $recipeRepo->getVegetarianRecipes();
        } else {
            $recipes = $recipeRepo->getAllRecipes();
        }

        // populate arrays
        foreach ($recipes as $recipe) {
            switch ($recipe['type']) {
            case 'petit déjeuner':
                $this->breakfast[] = $recipe; 
                break;
            case 'déjeuner':
                $this->lunchs[] = $recipe;
                break;
            case 'dîner':
                $this->dinners[] = $recipe;
                break;
            }
        }

        // price filter
        $lunchs = $this->getTargetedRecipesWithPrice($this->lunchs);
        $dinners = $this->getTargetedRecipesWithPrice($this->dinners);
        $breakfast = $this->getTargetedRecipesWithPrice($this->breakfast);

        // randomize
        $menuFirst = $this->shuffleMenu($breakfast, $lunchs, $dinners);

        // price filter 2
        $menuSecond = $this->adjustMenu($menuFirst['menu'], $menuFirst['menuLeft']);

        return $menuSecond;
    }


    /**
     * Filters recipes against target price
     * Returns an array like : [recipeId => [price => 2, type => dejeuner]]
     *
     * @return array $recipesArray
     *
     */
    public function getTargetedRecipesWithPrice(array $recipes)
    {
        $repository = $this->em->getRepository(Recipe::class);

        $recipesArray = [];

        // Set up a limit, in %, beyond wich the recipe is excluded
        // NB : we enlarge the limit for breakfasts because 
        // we have too few of them for now in database
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
            // actual variation, in %, between recipe price and target price
            $variation = ($price - ($this->targetPrice)) / ($this->targetPrice) * 100;

            // if variation is within limits, add it to array
            if ($variation < $limit && $variation > -$limit) {
                $recipesArray[$recipe['id']] = [
                    'price' => $price,
                    'type' => $recipe['type']
                ];
            } 
        }
        return $recipesArray;
    }

    /**
     * Build menu from random values 
     *
     * @return array [$menus, $menusLeft]
     *
     */
    public function shuffleMenu($breakfast, $lunchs, $dinners)
    {
        // array with selected recipes
        $menu = [];
        // array with not selected recipes
        $menuLeft = [];

        $quantity = self::QUANTITY / 3;

        // temporary arrays ([numKey => recipeId]) to be able do draw random numbers
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

        // built menu "left" for helping to create
        // menu V2 (if necessary) in next step
        $allRecipes = $breakfast + $lunchs + $dinners;
        foreach ($allRecipes as $key => $value) {
            if (!array_key_exists($key, $menu)) {
                $menuLeft[$key] = $value;
            }
        }

        return ['menu' => $menu, 'menuLeft' => $menuLeft];
    }

    /**
     * Corrects generated menu's price to make it 
     * the closest posible to user's budget
     *
     * @return array $menu
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
}

