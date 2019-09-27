<?php

namespace App\Controller;

use App\Entity\Recipe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/api/recipe", name="recipe_")
 */
class RecipeController extends AbstractController
{
    /**
     * Find a recipe (READ)
     *
     * @Route(
     *      "/{recipe}",
     *      name="find",
     *      methods={"GET"}, 
     *      requirements={"recipe": "\d"}
     * )
     */
    public function find(Recipe $recipe)
    {
        return $this->json([
            'message' => 'hello RecipeController->find()',
            'recipe' => $recipe,
        ]);
    }

    /**
     * Find ingredients of a recipe
     *
     * @Route(
     *      "/{recipe}/ingredients",
     *      name="find_ingredients",
     *      methods={"GET"},
     *      requirements={"recipe": "\d"}
     * )
     */
    public function findIngredients(Recipe $recipe)
    {
        return $this->json([
            'message' => 'hello RecipeController->findIngredients()',
            'ingredients' => $recipe->getIngredients(),
        ]);
    }

    /**
     * Find steps of a recipe
     *
     * @Route(
     *      "/{recipe}/steps",
     *      name="find_steps",
     *      methods={"GET"},
     *      requirements={"recipe": "\d"}
     * )
     */
    public function findSteps(Recipe $recipe)
    {
        return $this->json([
            'message' => 'hello RecipeController->findSteps()',
            'steps' => $recipe->getRecipeSteps(),
        ]);
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
        return $this->json('hello RecipeController->create()');
    }

    /**
     * Update a recipe
     *
     * @Route(
     *      "/{recipe}",
     *      name="update",
     *      methods={"PUT"}, 
     *      requirements={"recipe": "\d"}
     * )
     */
    public function update(Recipe $recipe)
    {
        return $this->json([
            'message' => 'hello RecipeController->update()',
            'recipe' => $recipe,
        ]);
    }

    /**
     * Delete a recipe
     *
     * @Route(
     *      "/{recipe}",
     *      name="delete",
     *      methods={"DELETE"}, 
     *      requirements={"recipe": "\d"}
     * )
     */
    public function delete(Recipe $recipe)
    {
        return $this->json([
            'message' => 'hello RecipeController->delete()',
            'recipe' => $recipe,
        ]);
    }
}
