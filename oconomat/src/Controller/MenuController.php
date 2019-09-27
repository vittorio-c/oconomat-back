<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


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
    public function find()
    {
        return $this->json('hello MenuController->find()');
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
    public function update()
    {
        return $this->json('hello MenuController->update()');
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
    public function delete()
    {
        return $this->json('hello MenuController->delete()');
    }
}
