<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Entity\User;
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
 * @Route("/api/menu", name="menu_")
 */
class MenuController extends AbstractController
{
    /**
     * Find a menu (READ)
     * TODO try to export that methods as an external service
     * in order to keep the controller thin
     *
     * @Route(
     *      "/{menu}",
     *      name="find",
     *      methods={"GET"}, 
     *      requirements={"menu": "\d*"}
     * )
     */
    public function find(Menu $menu)
    {
        $encoder = [new JsonEncoder(new JsonEncode(JSON_UNESCAPED_SLASHES))];

        // get User url as a callback
        $userUrl = function ($user) {
            $url = $this->generateUrl(
                'user_find',
                ['user' => $user->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL
            );
            return $url; 
        };

        // get Recipe urls as a callback
        $recipeUrl = function ($recipes) {
            $urls = [];
            foreach ($recipes as $recipe) {
                $urls[] = $this->generateUrl(
                    'recipe_find',
                    ['recipe' => $recipe->getId()],
                    UrlGeneratorInterface::ABSOLUTE_URL
                );
            }
            return $urls;
        };

        // set up the callbacks on user and recipes
        $defaultContext = [
            AbstractNormalizer::CALLBACKS => [
                'user' => $userUrl,
                'recipes' => $recipeUrl,
            ],
        ];

        // set up normalizer with the callbacks attached
        $normalizers = [
            new DateTimeNormalizer(),
            new GetSetMethodNormalizer(null, null, null, null, null, $defaultContext)
        ];

        // set up serializer
        $serializer = new Serializer($normalizers, $encoder);

        // normalize with the data we want
        $data = $serializer->normalize($menu, null, [
            'attributes' => [
                'id', 'createdAt', 'updatedAt',
                'user' => ['id'],
                'recipes' => ['id']
            ]
        ]);

        // encode in json
        $data = $serializer->encode($data, 'json');

        return new Response($data, 200, ['Content-Type' => 'application/json']);
    }

    /**
     * Update a menu
     *
     * @Route(
     *      "/{menu}",
     *      name="update",
     *      methods={"PUT"}, 
     *      requirements={"menu": "\d"}
     * )
     */
    public function update(Menu $menu)
    {
        return $this->json([
            'message' => 'hello MenuController->update()',
            'menu' => $menu
        ]);
    }

    /**
     * Delete a menu
     *
     * @Route(
     *      "/{menu}",
     *      name="delete",
     *      methods={"DELETE"}, 
     *      requirements={"menu": "\d"}
     * )
     */
    public function delete(Menu $menu)
    {
        return $this->json([
            'message' => 'hello MenuController->delete()',
            'menu' => $menu
        ]);
    }

    /**
     * Get shopping-list from menu
     *
     * @Route(
     *      "/{menu}/shopping-list",
     *      name="shopping_list",
     *      methods={"GET"},
     *      requirements={"menu": "\d*"}
     * )
     */
    public function shoppingList(Menu $menu)
    {
        // get shopping list
        $data = $this->getDoctrine()
                     ->getRepository(Menu::class)
                     ->getShoppinigListFromMenuId($menu->getId());

        // construct shopping list's metadatas
        $metadata = [
            'menuId' => $menu->getId(),
            'createdAt' => $menu->getCreatedAt(),
            'userId' => $menu->getUser()->getId()
        ];

        // prepare php array
        $shoppingList = [];
        $shoppingList['metadata'] = $metadata;
        $shoppingList['shoppingList'] = $data;
        // serialize and send 
        return $this->json($shoppingList);
    }

    /**
     * Get last menu from user
     *
     * @Route(
     *      "/user/{user}/last",
     *      name="last",
     *      methods={"GET"},
     *      requirements={"user": "\d*"}
     * )
     */
    public function lastMenu(User $user)
    {
        $em = $this->getDoctrine()->getRepository(Menu::class);
        $menu = $em->findOneBy(['user' => $user->getId()], ['createdAt' => 'DESC']);
        return $this->redirectToRoute('menu_find', ['menu' => $menu->getId()], 301);
    }

    /**
     * Renew a menu when there are no modifications in user's objectives
     *
     * @Route(
     *  "/renew",
     *  name="renew",
     *  methods={"POST"}
     * )
     */
    public function renew()
    {
        return $this->json('hello MenuController->renew()');
    }
}
