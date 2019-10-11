<?php

namespace App\Serializer\Normalizer;

use App\Entity\Menu;
use App\Entity\Objectif;
use App\Entity\Recipe;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Menu normalizer
 */
class RecipeNormalizer implements NormalizerInterface
{
    private $em;

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = array())
    {
        $recipeSteps = [];

        $userQuantity = $context['metaData']['userQuantity'] ?? 1;

        foreach ($object->getRecipeSteps() as $step) {
            $recipeSteps[] = [
                'stepNumber' => $step->getStepNumber(),
                'content' => $step->getContent()
            ];
        }

        $ingredients = [];

        foreach ($object->getIngredients() as $ingredient) {
            $aliments = [];
            $aliments[] = [
                'name' => $ingredient->getAliment()->getName(), 
                'unit' => $ingredient->getAliment()->getUnit()
            ];

            $ingredients[] = [
                'quantity' => $ingredient->getQuantity() * $userQuantity,
                'aliment' => $aliments
            ];
        }

        $data = [
            'id' => $object->getId(),
            'title' => $object->getTitle(),
            'slug' => $object->getSlug(),
            'type' => $object->getType(),
            'createdAt' => $object->getCreatedAt()->format('Y-m-d'),
            'image' => $object->getImage(),
            'recipeSteps' => $recipeSteps,
            'ingredients' => $ingredients
        ];

        if (isset($context)) {
            $data = $context + $data;
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Recipe;
    }

}
