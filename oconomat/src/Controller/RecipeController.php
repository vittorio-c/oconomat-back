<?php

namespace App\Controller;

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
    public function find()
    {
        return $this->json('hello RecipeController->find()');
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
    public function findIngredients()
    {
        return $this->json('hello RecipeController->findIngredients()');
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
    public function findSteps()
    {
        return $this->json('hello RecipeController->findSteps()');
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
    public function update()
    {
        return $this->json('hello RecipeController->update()');
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
    public function delete()
    {
        return $this->json('hello RecipeController->delete()');
    }
}
