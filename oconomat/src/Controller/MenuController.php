<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Entity\User;
use App\Serializer\Normalizer\MenuNormalizer;
use App\Service\CheckOwnership;
use App\Service\ShoppingList;
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
     *
     * @Route(
     *      "/{menu}",
     *      name="find",
     *      methods={"GET"}, 
     *      requirements={"menu": "\d*"}
     * )
     */
    public function find(Menu $menu, MenuNormalizer $menuNormalizer, CheckOwnership $checkOwnership)
    {
        $owner = $checkOwnership->check($menu);

        if ($owner === true) {
            $serializer = new Serializer([$menuNormalizer], $this->encoder);
            $data = $serializer->serialize($menu, 'json');
            return new Response(
                $data,
                200,
                ['Content-Type' => 'application/json']
            );
        } else {
            return $this->json(
                $checkOwnership->getData(),
                $checkOwnership->getCode()
            );
        }
    }

    /**
     * Update a menu
     *
     * NOT DONE YET
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
     * NOT DONE YET
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
    public function shoppingList(Menu $menu, CheckOwnership $checkOwnership, ShoppingList $shoppingList)
    {
        $owner = $checkOwnership->check($menu);

        if ($owner === true) {
            $data = $shoppingList->generate($menu);
            // serialize and send 
            return $this->json($data);

        } else {
            return $this->json(
                $checkOwnership->getData(),
                $checkOwnership->getCode()
            );
        }
    }

    /**
     * Get last menu from connected user
     *
     * @Route(
     *      "/user/last",
     *      name="last",
     *      methods={"GET"}
     * )
     */
    public function lastMenu(MenuNormalizer $menuNormalizer)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getRepository(Menu::class);
        $menu = $em->findOneBy(['user' => $user->getId()], ['createdAt' => 'DESC']);

        // if menu exists
        if ($menu) {
            $serializer = new Serializer([$menuNormalizer], $this->encoder);
            $data = $serializer->serialize($menu, 'json');
            return new Response($data, 200, ['Content-Type' => 'application/json']);
        }

        // else : 404 TODO ExceptionListener
        $data = [
            'status' => 404,
            'message' => 'L\'utilisateur connecté ne possède pas encore de menu.'
        ];
        return $this->json($data, 404);
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
