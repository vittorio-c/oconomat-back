<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

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
     *      requirements={"user": "\d*"}
     * )
     */
    public function find(User $user)
    {
        // TODO : mettre uniquement un lien pour le menu
        // TODO : pour les objectifs, mettre le "currentObjectif" uniqueemnt
        $encoder = [new JsonEncoder()];
        $normalizers = array(new DateTimeNormalizer(), new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoder);

        // normalize with the data we want
        $data = $serializer->normalize($user, null, [
            'attributes' => [
                'id', 'email', 'roles', 'firstname', 'lastname', 'createdAt', 'updatedAt',
                'objectifs' => ['budget'],
                'menus' => ['id', 'createdAt', 'updatedAt', 'recipes' => ['id']]
            ]
        ]);

        // encode in json
        $data = $serializer->encode($data, 'json');

        return new Response($data, 200, ['Content-Type' => 'application/json']);
    }

    /**
     * Update an existing user
     *
     * @Route(
     *      "/{user}",
     *      name="update",
     *      methods={"PUT"},
     *      requirements={"user": "\d*"}
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
