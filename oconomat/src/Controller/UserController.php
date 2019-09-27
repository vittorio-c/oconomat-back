<?php

namespace App\Controller;

use App\Entity\User;
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
    public function find(User $user)
    {
        return $this->json([
            'message' => 'hello UserController->find()',
            'user' => $user,
        ]);
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
    public function update(User $user)
    {
        return $this->json([
            'message' => 'hello UserController->update()',
            'user' => $user,
        ]);
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
    public function delete(User $user)
    {
        return $this->json([
            'message' => 'hello UserController->delete()',
            'user' => $user,
        ]);
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
