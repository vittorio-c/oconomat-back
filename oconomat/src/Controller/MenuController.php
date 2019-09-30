<?php

namespace App\Controller;

use App\Entity\Menu;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;


/**
 * @Route("/api/menu", name="menu_")
 */
class MenuController extends AbstractController
{
    /**
     * Find a menu (READ)
     *
     * @Route(
     *      "/{menu}",
     *      name="find",
     *      methods={"GET"}, 
     *      requirements={"menu": "\d"}
     * )
     */
    public function find(Menu $menu)
    {
        $encoder = [new JsonEncoder()];
        $normalizers = array(new DateTimeNormalizer(), new ObjectNormalizer());
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
     * Create a new menu
     *
     * @Route(
     *      "/create",
     *      name="create",
     *      methods={"POST"}
     * )
     */
    public function create()
    {
        return $this->json('hello MenuController->create()');
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
     *      requirements={"menu": "\d"}
     * )
     */
    public function shoppingList()
    {
        return $this->json('hello MenuController->shoppingList()');
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
