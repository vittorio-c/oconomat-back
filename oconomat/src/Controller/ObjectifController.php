<?php

namespace App\Controller;

use App\Controller\RecipeController;
use App\Entity\User;
use App\Entity\Menu;
use App\Entity\Objectif;
use App\Entity\Recipe;
use App\Form\ObjectifType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;


/**
 * @Route("/api/objectif", name="objectif_")
 */
class ObjectifController extends AbstractController
{
    /**
     * @Route(
     *      "/menu/generate",
     *      name="generate_menu",
     *      methods="POST",
     * )
     */
    public function generateMenu(Request $request)
    {
        $form = $this->createForm(ObjectifType::class);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);
        $doctrine = $this->getDoctrine();

        if ($form->isValid()) {
            $user = $this->getUser();

            $budget = $data['budget'];
            $quantity = 14;
            // prix objectif par recette
            $targetPrice = round($budget / $quantity);

            $lunchs = $doctrine->getRepository(Recipe::class)->findBy(['type' => 'dejeuner']);
            $dinners = $doctrine->getRepository(Recipe::class)->findBy(['type' => 'diner']);

            // on écrase les tableaux précédents avec des tableaux 
            // contenant uniquement des recettes correspondant plus ou moins au prix objectif
            $lunchs = $this->getRecipesPrice($lunchs, $targetPrice);
            $dinners = $this->getRecipesPrice($dinners, $targetPrice);

            // construction du menu
            $menus = $this->buildMenu($lunchs, $dinners, $quantity);

            // calcul du prix total du menu
            $total = $this->getMenuTotalPrice($menus['menu']);

            // ajustements pour être sur de ne pas dépasser le prix
            $menu = $this->adjustMenu($menus['menu'], $menus['menuLeft'], $budget);

            // passage en objets et enregistrements bdd
            $menuObject = new Menu();
            foreach ($menu as $key => $value) {
                $repository = $doctrine->getRepository(Recipe::class);
                $recipe = $repository->find($key);
                $menuObject->addRecipe($recipe);
            }

            $menuObject->setUser($user);

            $objectives = $form->getData();
            $objectives->setUser($user);

            $em = $doctrine->getManager();
            $em->persist($objectives);
            $em->persist($menuObject);

            $em->flush();
            return $this->json("menu créé et enregsitré en bdd");
        } else {
            return $this->json("raté");
        }
    }

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

    public function getMenuTotalPrice($menu)
    {
        $total = 0;
        foreach ($menu as $id => $value) {
            $total += $value['price'];
        }
        return $total;
    }

    public function getRecipesPrice(array $recipes, $targetPrice)
    {
        $em = $this->getDoctrine()->getRepository(Recipe::class);

        // tableau avec id de la  recette et prix total
        $array = [];
        foreach ($recipes as $recipe) {
            $price = $em->getRecipieTotalPrice($recipe->getId()); 
            $diff = $price[0]['totalPrice'] - $targetPrice;

            if ($diff <= 5 && $diff >= -5) {
                $array[$recipe->getId()] = [
                    'price' => intval($price[0]['totalPrice']),
                    'type' => $recipe->getType()
                ];
            }
        }
        return $array;
    }

    public function getTargetedRecipesWithPrices($recipes, $targetPrice)
    {
        $newRecipes = [];
        foreach ($recipes as $recipe) {
            $diff = intval($recipe['price']) - $targetPrice;

            if ($diff <= 5 && $diff >= -5) {
                $newRecipes[] = $recipe;
            }
        }
        $newRecipes = array_column($newRecipes, 'price', 'id');
        return $newRecipes;
    }

    public function buildMenu($lunchs, $dinners, $quantity)
    {
        // tableau menu avec les 21 recettes sélectionnées
        $menu = [];
        // tableau avec les recettes non sélectionnées
        $menuLeft = [];
        $allRecipes = $lunchs + $dinners;

        // tableau numérique temporaire ([numKey => recipeId]) pour pouvoir tirer au hasard
        // une recette sur la base du nombre généré plus bas
        $arrayLunchs = array_keys($lunchs);
        $arrayDinners = array_keys($dinners);

        // 7 lunchs
        for ($i = 0; $i < $quantity / 2; $i++) {
            $n = rand(0, count($lunchs) - 1);
            $key = $arrayLunchs[$n];
            $value = $lunchs[$key];
            if (!array_key_exists($key, $menu)) {
                $menu[$key] = $value;
            } else {
                $i--;
                continue;
            }
        }

        // 7 dinners
        for ($i = 0; $i < $quantity / 2; $i++) {
            $n = rand(0, count($dinners) - 1);
            $key = $arrayDinners[$n];
            $value = $dinners[$key];
            if (!array_key_exists($key, $menu)) {
                $menu[$key] = $value;
            } else {
                $i--;
                continue;
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
