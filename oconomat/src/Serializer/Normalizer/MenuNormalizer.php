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

        $recipesArray = [];
        foreach ($recipes as $recipe) {
            $id = $recipe->getId();
            $url = $this->getUrl('recipe_find', ['recipe' => $id], UrlGeneratorInterface::ABSOLUTE_URL);
            $type = $recipe->getType();
            $price = $this->em->getRepository(Recipe::class)->getRecipieTotalPrice($id);
            $recipesArray[] = [
                'id' => $id,
                'url' => $url,
                'type' => $type,
                'price' => intval($price[0]['totalPrice'])
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
