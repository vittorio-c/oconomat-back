<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/api/user", name="user_")
 */
class UserController extends AbstractController
{
    /**
     * Find an user (READ)
     *
     * @Route(
     *      "/{user}",
     *      name="find",
     *      methods={"GET"},
     *      requirements={"user": "\d"}
     * )
     */
    public function find()
    {
        return $this->json('hello UserController->find()');
    }

    /**
     * Update an existing user
     *
     * @Route(
     *      "/{user}",
     *      name="update",
     *      methods={"PUT"},
     *      requirements={"user": "\d"}
     * )
     */
    public function update()
    {
        return $this->json('hello UserController->update()');
    }

    /**
     * Delete an user
     *
     * @Route(
     *      "/{user}",
     *      name="create",
     *      methods={"DELETE"}
     * )
     */
    public function delete()
    {
        return $this->json('hello UserController->delete()');
    }

    /**
     * Process contact form
     *
     * @Route(
     *  "/contact",
     *  name="contact",
     *  methods={"POST"}
     * )
     */
    public function contact()
    {
        return $this->json('hello UserController->contact()');
    }
}
