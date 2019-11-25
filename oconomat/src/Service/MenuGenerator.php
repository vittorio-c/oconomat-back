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

    /**
     * @var bool
     */
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

        // target price = requested price per recipe
        $this->targetPrice = round($this->budget / self::QUANTITY, 2);

        // global average price of all recipes in DB
        // multiplied by number of users
        $averagePriceFromDB = floatval(
            $recipeRepo->getAllRecipiesAveragePrice()[0]['average']
        ) * $this->users;

        // $variation represents how much the requested price per recipe
        // is far from the average price per recipe we have in DB
        $variation = ($this->targetPrice - $averagePriceFromDB) / $averagePriceFromDB * 100;

        // if that variation is too wide, i.e. requested budget is too low or too high
        // we can't provide correct output. Abord.
        if ($variation < -50 || $variation > 50) {
            // TODO rise exception instead 
            return false;
        }

        // let's set up some arrays
        $lunchs = [];
        $dinners = [];
        $breakfast = [];

        // get all recipes or only vegetarian ones
        if ($vegetarian === true) {
            $recipes = $recipeRepo->getVegetarianRecipes();
        } else {
            $recipes = $recipeRepo->getAllRecipes();
        }

        // populate our arrays
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

        // For each type of recipe, we only want those whose price is close to the target price
        $lunchs = $this->getTargetedRecipesWithPrice($lunchs);
        $dinners = $this->getTargetedRecipesWithPrice($dinners);
        $breakfast = $this->getTargetedRecipesWithPrice($breakfast);

        // lets finally build our Menu V1 !
        $menus = $this->buildMenu($breakfast, $lunchs, $dinners);

        // In case the menu is still above user's budget, let's make the V2
        $menu = $this->adjustMenu($menus['menu'], $menus['menuLeft']);

        return $menu;
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
        //
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
    public function buildMenu($breakfast, $lunchs, $dinners)
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
}

