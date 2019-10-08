<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Entity\User;
use App\Serializer\Normalizer\MenuNormalizer;
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
    private $encoder;

    public function __construct()
    {
        $this->encoder = [new JsonEncoder(new JsonEncode(JSON_UNESCAPED_SLASHES))];
    }

    /**
     * Find a menu (READ)
     * TODO handle exception on this method
     *
     * @Route(
     *      "/{menu}",
     *      name="find",
     *      methods={"GET"}, 
     *      requirements={"menu": "\d*"}
     * )
     */
    public function find(Menu $menu, MenuNormalizer $menuNormalizer)
    {
        $serializer = new Serializer([$menuNormalizer], $this->encoder);
        $data = $serializer->serialize($menu, 'json');

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

        // merge duplicate items
        $newData = [];
        foreach ($data as $key => $value) {
            $arrayTemp = array_column($newData, 'foodId');
            if (!in_array($value['foodId'], $arrayTemp)) {
                $newData[] = $value;
            } else {
                $id = array_search($value['foodId'], $arrayTemp);
                dump($id);
                $newData[$id]['quantity'] += $value['quantity'];
                $newData[$id]['totalPrice'] += $value['totalPrice'];
            }
        }
        $data = $newData;

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
    public function lastMenu(User $user, MenuNormalizer $menuNormalizer)
    {
        $em = $this->getDoctrine()->getRepository(Menu::class);
        $menu = $em->findOneBy(['user' => $user->getId()], ['createdAt' => 'DESC']);

        if ($menu) {
            $serializer = new Serializer([$menuNormalizer], $this->encoder);
            $data = $serializer->serialize($menu, 'json');
            return new Response($data, 200, ['Content-Type' => 'application/json']);
        } else {
            $data = json_encode([
                'status' => 404,
                'message' => 'Cet utilisateur ne possède pas encore de menu.'
            ]);
            return new Response($data, 404, ['Content-Type' => 'application/json']);
        }
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
