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
        $targetPrice = $budget / $quantity;

        $em = $this->getDoctrine()->getRepository(Recipe::class);
        $recipes = $em->findAll();

        // tableau avec toutes les recettes et leur prix total
        $recipesPrice = [];
        foreach ($recipes as $recipe) {
            $price = $em->getRecipieTotalPrice($recipe->getId()); 
            //dump($price);
            $recipesPrice[] = ['id' => $recipe->getId(), 'price' => intval($price[0]['totalPrice'])];
        }

        // tableau avec uniquement des recettes correspondant plus ou moins au prix objectif
        $recipesTarget = [];

        foreach ($recipesPrice as $recipe) {
            $diff = intval($recipe['price']) - $targetPrice;

            if ($diff <= 5 && $diff >= -5) {
                $recipesTarget[] = $recipe;
            }
        }
        $recipesTarget = array_column($recipesTarget, 'price', 'id');

        // tableau menu avec les 21 recettes
        $menu = [];
        // tableau numérique temporaire ([numKey => recipeId]) pour pouvoir tirer au hasard
        // une recette sur la base du nombre généré plus bas
        $a = array_keys($recipesTarget);
        dump('recipesTarget', $recipesTarget);
        dump('tempArray', $a);

        // built menu with random recipes
        for ($i = 0; $i < $quantity; $i++) {
            // génére un nb au hasard compris dans la range du nombre total de recettes
            $n = rand(0, count($recipesTarget) - 1);
            // stocke dans $key l'id de la recette générée au hasard
            $key = $a[$n];
            // stocke dans $value la valeur (prix total) de la recette générée au hasard
            $value = $recipesTarget[$key];

            if (!array_key_exists($key, $menu)) {
                // construit le nouveau tableau menu si l'item n'est pas déjà présent
                $menu[$key] = $value;
            } else {
                // sinon, relance la boucle au même stade
                $i--;
                continue;
            }
        }

        // tableau avec les recettes non sélectionnées
        $left = [];
        foreach ($recipesTarget as $key => $value) {
            if (!array_key_exists($key, $menu)) {
                $left[$key] = $value;
            }
        }
        dump('left', $left);
        dump('menu', $menu);

        // calcul du prix total du menu
        $total = 0;
        foreach ($menu as $m) {
            $total += $m;
        }
        dump('total : ' . $total);
        dump('budget : ' . $data['budget']);

        if ($total <= $data['budget']) {
            dump('yeah');
        } else {
            // get id of most expensive recipe from $menu
            // NB max et min retournent la valeur de l'élément trouvé, et non l'index
            // il faut donc chercher ensuite avec array_search
            $max = array_search(max($menu), $menu);
            dump($max);
            // get id of lowest expensive recipe from $recipeTarget
            $min = array_search(min($left), $left);
            dump($min);

            // enlever le plus chere
            unset($menu[$max]);
            // ajouter le moins cher
            $menu[$min] = $recipesTarget[$min];
            //array_splice($menu, $max, 1);
            dump($menu);
            //$min = 
            //dump($prices);
            //dump($max);
            dump('nope');
        }
        exit;


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

    public function adjustMenu($menu)
    {

    }
}
