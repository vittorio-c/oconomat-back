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
class MenuNormalizer implements NormalizerInterface
{
    private $router;
    private $em;

    public function __construct(UrlGeneratorInterface $router, EntityManagerInterface $em)
    {
        $this->router = $router;
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = array())
    {
        // get metadata
        $user = $object->getUser(); // ex: menu 238 = userId 65
        $objectif = $object->getObjectif();
        $userQuantity = null !== $objectif ? $objectif->getUserQuantity() : 1;
        $vegetarian = null !== $objectif ? $objectif->getVegetarian() : false;
        $createdAt = $object->getCreatedAt()->format('Y-m-d');
        $updatedAt = $object->getUpdatedAt() ? $object->getUpdatedAt()->format('Y-m-d') : null;

        // generate URL for user
        $userUrl = $this->getUrl(
            'user_find', 
            ['user' => $user->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        // build array of recipes
        $recipesArray = [];
        $recipes = $object->getRecipes();
        $recipeRepo = $this->em->getRepository(Recipe::class);
        foreach ($recipes as $recipe) {
            $id = $recipe->getId();
            $url = $this->getUrl('recipe_find', ['recipe' => $id], UrlGeneratorInterface::ABSOLUTE_URL);
            $type = $recipe->getType();
            $price = round($recipeRepo->getRecipieTotalPrice($id)[0]['totalPrice'] * $userQuantity, 2);
            $image = $recipe->getImage();
            $title = $recipe->getTitle();
            // add current recipe to end of array
            $recipesArray[] = [
                'id' => $id,
                'title' => $title,
                'url' => $url,
                'type' => $type,
                'price' => $price,
                'image' => $image
            ];
        }

        // build final array
        $data = [
            'idMenu' => $object->getId(),
            'createdAt' => $createdAt,
            'updatedAt' => $updatedAt,
            'userQuantity' => $userQuantity,
            'vegetarian' => $vegetarian,
            'user' => [
                'id' => $user->getId(),
                'url' => $userUrl
            ],
            'recipes' => $recipesArray
        ];

        // if $context is set, prepend it 
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
        return $data instanceof Menu;
    }

    public function getUrl($routeName, $urlParamater = [], $reference)
    {
        return $this->router->generate($routeName, $urlParamater, $reference);
    }
}
