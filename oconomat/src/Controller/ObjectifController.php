<?php

namespace App\Controller;

use App\Controller\RecipeController;
use App\Controller\MenuController;
use App\Entity\User;
use App\Entity\Menu;
use App\Entity\Objectif;
use App\Entity\Recipe;
use App\Form\ObjectifType;
use App\DTO\ObjectifDto;
use App\Service\MenuGenerator;
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
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\ParameterBag;


/**
 * @Route("/api/objectif", name="objectif_")
 */
class ObjectifController extends AbstractController
{
    // user's objectives
    private $userQuantity;
    private $vegetarian;
    private $budget;

    /**
     * Generate a menu from user's objectives
     *
     * @Route(
     *      "/menu/generate",
     *      name="generate_menu",
     *      methods="POST",
     * )
     */
    public function generateMenu(
        Request $request,
        MenuNormalizer $menuNormalizer,
        MenuGenerator $menuGenerator,
        ValidatorInterface $validator
    )
    {
        $data = json_decode(
            strip_tags($request->getContent()),
            true);

        $dto = ObjectifDto::fromRequestData($data);
        $errors = $validator->validate($dto);

        if (count($errors) > 0) {
            $data = [];
            $data['code status'] = 400;
            foreach ($errors as $error) {
                $data['errors'][$error->getPropertyPath()] = $error->getMessage();
            }
            return $this->json($data, 400);
        } 

        $this->budget = $data['budget'];
        $this->userQuantity = $data['userQuantity'] ?? 1; 
        $this->vegetarian = $data['vegetarian'];

        $menu = $menuGenerator->generateMenu($this->budget, $this->userQuantity, $this->vegetarian);

        // TODO if possible (see frontend) set 202 instead of 404
        if ($menu === false) {
            $data = json_encode([
                'code status' => 404,
                'message' => 'Budget is too high or too low. Please try again.'
            ]);
            return new Response($data, 404, ['Content-Type' => 'application/json']);
        }

        // total price
        $totalPrice = round($menuGenerator->getMenuTotalPrice($menu), 2);

        $menuObject = $this->saveMenu($menu);

        // serialization and response
        $encoder = [new JsonEncoder(new JsonEncode(JSON_UNESCAPED_SLASHES))];
        $serializer = new Serializer([$menuNormalizer], $encoder);

        $context['metadata'] = [
            'status' => 200,
            'message' => 'Menu généré avec succès.',
            'budget' => $this->budget,
            'totalPrice' => $totalPrice,
            'userQuantity' => $this->userQuantity
        ];

        $data = $serializer->serialize($menuObject, 'json', $context);

        return new Response($data, 200, ['Content-Type' => 'application/json']);
    }

    public function saveMenu($menu)
    {
        $doctrine = $this->getDoctrine();
        $user = $this->getUser();

        $menuObject = new Menu();

        foreach ($menu as $key => $value) {
            $repository = $doctrine->getRepository(Recipe::class);
            $recipe = $repository->find($key);
            $menuObject->addRecipe($recipe);
        }

        $menuObject->setUser($user);

        $objectives = new Objectif();
        $objectives->setUser($user);
        $objectives->setBudget($this->budget);
        $objectives->setUserQuantity($this->userQuantity);
        // ici
        $objectives->setVegetarian($this->vegetarian);

        $menuObject->setObjectif($objectives);

        $em = $doctrine->getManager();
        $em->persist($objectives);
        $em->persist($menuObject);

        $em->flush();

        return $menuObject;
    }

    /**
     * @Route(
     *      "/budget/last/{id}",
     *      name="budget_last",
     *      methods="GET",
     * )
     */
    public function getLastBudget(User $user)
    {
        $userBudget = $this->getDoctrine()->getRepository(Objectif::class)->getLastBudget($user);

        return $this->json($userBudget[0]->getBudget());
    }

}
