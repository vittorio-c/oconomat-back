<?php

namespace App\Serializer\Normalizer;

use App\Entity\Menu;
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
        $user = $object->getUser();
        $userUrl = $this->getUrl('user_find', ['user' => $user->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        $recipes = $object->getRecipes();
        $createdAt = $object->getCreatedAt()->format('Y-m-d');
        $updatedAt = $object->getUpdatedAt() ? $object->getUpdatedAt()->format('Y-m-d') : null;
        $recipeRepo = $this->em->getRepository(Recipe::class);

        $recipesArray = [];
        foreach ($recipes as $recipe) {
            $id = $recipe->getId();
            $url = $this->getUrl('recipe_find', ['recipe' => $id], UrlGeneratorInterface::ABSOLUTE_URL);
            $type = $recipe->getType();
            $price = round($recipeRepo->getRecipieTotalPrice($id)[0]['totalPrice'], 2);
            $image = $recipe->getImage();
            $title = $recipe->getTitle();
            $recipesArray[] = [
                'id' => $id,
                'title' => $title,
                'url' => $url,
                'type' => $type,
                'price' => $price,
                'image' => $image
            ];
        }

        $data = [
            'idMenu' => $object->getId(),
            'createdAt' => $createdAt,
            'updatedAt' => $updatedAt,
            'user' => [
                'id' => $user->getId(),
                'url' => $userUrl
            ],
            'recipes' => $recipesArray
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
        return $data instanceof Menu;
    }

    public function getUrl($routeName, $urlParamater = [], $reference)
    {
        return $this->router->generate($routeName, $urlParamater, $reference);
    }
}
