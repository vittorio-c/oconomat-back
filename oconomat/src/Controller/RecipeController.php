<?php

namespace App\Controller;

use App\Entity\Recipe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;


/**
 * @Route("/api/recipe", name="recipe_")
 */
class RecipeController extends AbstractController
{
    /**
     * Find a recipe (READ) with every piece of information
     *
     * @Route(
     *      "/{recipe}",
     *      name="find",
     *      methods={"GET"}, 
     *      requirements={"recipe": "\d*"}
     * )
     */
    public function find(Recipe $recipe)
    {
        // set up the serializer
        // TODO make it as a service
        $encoder = [new JsonEncoder()];
        $normalizers = array(new DateTimeNormalizer(), new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoder);

        // normalize with the data we want
        $data = $serializer->normalize($recipe, null, [
            'attributes' => [
                'id', 'title', 'slug', 'type', 'createdAt', 'updatedAt',
                'recipeSteps' => ['stepNumber', 'content'],
                'ingredients' => ['quantity', 'aliment' => ['name', 'unit']]
            ]
        ]);

        // encode in json
        $data = $serializer->encode($data, 'json');

        return new Response($data, 200, ['Content-Type' => 'application/json']);
    }

    /**
     * Find ingredients of a recipe
     *
     * @Route(
     *      "/{recipe}/ingredients",
     *      name="find_ingredients",
     *      methods={"GET"},
     *      requirements={"recipe": "\d*"}
     * )
     */
    public function findIngredients(Recipe $recipe)
    {
        // set up the serializer
        $encoder = [new JsonEncoder()];
        $normalizers = [new DateTimeNormalizer(), new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoder);

        // normalize with the data we want
        $data = $serializer->normalize($recipe->getIngredients(), null, [
            'attributes' => [
                'quantity',
                'aliment' => ['name', 'unit', 'type']
            ]
        ]);

        // encode in json
        $data = $serializer->encode($data, 'json');

        return new Response($data, 200, ['Content-Type' => 'application/json']);
    }

    /**
     * Find steps of a recipe
     *
     * @Route(
     *      "/{recipe}/steps",
     *      name="find_steps",
     *      methods={"GET"},
     *      requirements={"recipe": "\d*"}
     * )
     */
    public function findSteps(Recipe $recipe)
    {
        // set up the serializer
        $encoder = [new JsonEncoder()];
        $normalizers = [new DateTimeNormalizer(), new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoder);

        // normalize with the data we want
        $data = $serializer->normalize($recipe->getRecipeSteps(), null, [
            'attributes' => [
                'stepNumber', 'content',
            ]
        ]);

        // encode in json
        $data = $serializer->encode($data, 'json');

        return new Response($data, 200, ['Content-Type' => 'application/json']);
    }

    /**
     * Create a new recipe
     *
     * @Route(
     *      "/create",
     *      name="create",
     *      methods={"POST"}
     * )
     */
    public function create()
    {
        // TODO : instantiate a new Recipe objet, then populate it with user's data, and persist it to database
        return $this->json('hello RecipeController->create()');
    }

    /**
     * Update a recipe
     *
     * @Route(
     *      "/{recipe}",
     *      name="update",
     *      methods={"PUT"}, 
     *      requirements={"recipe": "\d*"}
     * )
     */
    public function update(Recipe $recipe)
    {
        // TODO : modify object's properties and persist them to database

        // set up the serializer
        $encoder = [new JsonEncoder()];
        $normalizers = [new DateTimeNormalizer(), new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoder);

        // TODO : select and send data as json

        return $this->json('hello RecipeController->update()');
    }

    /**
     * Delete a recipe
     *
     * @Route(
     *      "/{recipe}",
     *      name="delete",
     *      methods={"DELETE"}, 
     *      requirements={"recipe": "\d*"}
     * )
     */
    public function delete(Recipe $recipe)
    {
        // TODO : delete objet from database

        return $this->json('hello RecipeController->delete()');
    }
}
