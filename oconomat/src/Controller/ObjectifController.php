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
        $data = json_decode($request->getContent(), true);

        $budget = $data['budget'];
        $quantity = 21;
        // prix objectif par recette
        $targetPrice = $budget / $quantity;

        $recipes = $this->getAllRecipesWithPrices();

        // on écrase le tableau précédent avec un tableau 
        // contenant uniquement des recettes correspondant plus ou moins au prix objectif
        $recipes = $this->getTargetedRecipesWithPrices($recipes, $targetPrice);

        $menus = $this->buildMenu($recipes, $quantity);

        // calcul du prix total du menu
        $total = $this->getMenuTotalPrice($menus['menu']);

        $menu = $this->adjustMenu($menus['menu'], $menus['menuLeft'], $budget);

        return $this->json($menu);
        exit;

        // LATER
        //$em = $this->getDoctrine()->getRepository(Recipe::class);
        //$totalPrice = $em->getRecipieTotalPrice(3);
        //dump($totalPrice);
        //exit;

        $form = $this->createForm(ObjectifType::class);

        $data = json_decode($request->getContent(), true);

        $form->submit($data);

        if ($form->isValid()) {
            $objectives = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($objectives);
            $em->flush();
            //return $this->json('nouvel objectif créé');
        }



        // j'ai le budget, et il est enregistré en bdd avec l'utilisateur relié
        // objectif : 
        //      - générer une liste de recette 
        //      - qui respecte le budget donné
        //      - l'associser à un nouveau menu
        // requis :
        //      - connaître le prix total par recette
        //      - générer pour lee moment 21 recettes (sans les classer par type)
        //      - il faut que la somme du prix de ces 21 recettes ne dépasse pas le budget
        //      - je peux les ajouter une par une pour le moment jusqu'à en avoir 21
        //      - puis comparer la somme avec le budget : si supérieur, tu enlève la plus chère
        //      - et tu restes
        //
        // au moment de la requete bdd, générer une colonne calculée pour le prix de la recette
        //
        //
    }

    public function adjustMenu($menu, $left, $budget)
    {
        while ($this->getMenuTotalPrice($menu) > $budget) {
            // NB max et min retournent la valeur de l'élément trouvé, et non l'index
            // il faut donc chercher ensuite avec array_search pour avoir l'id de la recette

            // get id of most expensive recipe from $menu
            $max = array_search(max($menu), $menu);
            dump('max supprimé : ', $menu[$max]);
            // get id of lowest expensive recipe from $left
            $min = array_search(min($left), $left);
            dump('min ajouté : ', $left[$min]);

            // enlever le plus chere du menu
            unset($menu[$max]);
            // ajouter le moins cher au menu
            $menu[$min] = $left[$min];
            // enlever le moins cher du $left pour 
            // être certain de ne pas retomber dessus à la prochaine boucle
            unset($left[$min]);
        }

        return $menu;
    }

    public function getMenuTotalPrice($menu)
    {
        $total = 0;
        foreach ($menu as $m) {
            $total += $m;
        }
        return $total;
    }

    public function getAllRecipesWithPrices()
    {
        $em = $this->getDoctrine()->getRepository(Recipe::class);
        $recipes = $em->findAll();

        // tableau avec toutes les recettes et leur prix total
        $array = [];
        foreach ($recipes as $recipe) {
            $price = $em->getRecipieTotalPrice($recipe->getId()); 
            $array[] = ['id' => $recipe->getId(), 'price' => intval($price[0]['totalPrice'])];
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

    public function buildMenu($recipes, $quantity)
    {
        // tableau menu avec les 21 recettes sélectionnées
        $menu = [];
        // tableau avec les recettes non sélectionnées
        $menuLeft = [];

        // tableau numérique temporaire ([numKey => recipeId]) pour pouvoir tirer au hasard
        // une recette sur la base du nombre généré plus bas
        $array = array_keys($recipes);

        // built menu with random recipes
        for ($i = 0; $i < $quantity; $i++) {
            // génére un nb au hasard compris dans la range du nombre total de recettes
            $n = rand(0, count($recipes) - 1);
            // stocke dans $key l'id de la recette générée au hasard
            $key = $array[$n];
            // stocke dans $value la valeur (prix total) de la recette générée au hasard
            $value = $recipes[$key];

            if (!array_key_exists($key, $menu)) {
                // construit le nouveau tableau menu si l'item n'est pas déjà présent
                $menu[$key] = $value;
            } else {
                // sinon, relance la boucle au même stade
                $i--;
                continue;
            }
        }

        // built menu left for futur purposes
        foreach ($recipes as $key => $value) {
            if (!array_key_exists($key, $menu)) {
                $menuLeft[$key] = $value;
            }
        }

        return ['menu' => $menu, 'menuLeft' => $menuLeft];
    }
}
