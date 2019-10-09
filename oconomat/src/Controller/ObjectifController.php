<?php

namespace App\Controller;

use App\Controller\RecipeController;
use App\Controller\MenuController;
use App\Entity\User;
use App\Entity\Menu;
use App\Entity\Objectif;
use App\Entity\Recipe;
use App\Form\ObjectifType;
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


/**
 * @Route("/api/objectif", name="objectif_")
 */
class ObjectifController extends AbstractController
{
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
        MenuGenerator $menuGenerator
    )
    {
        $form = $this->createForm(ObjectifType::class);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);
        $doctrine = $this->getDoctrine();

        if ($form->isValid()) {
            $user = $this->getUser();
            $budget = $data['budget'];
            $quantity = 14;

            //$menuGenerator = new MenuGenerator();
            $menu = $menuGenerator->generateMenu($budget, $quantity);

            // total
            $total = $menuGenerator->getMenuTotalPrice($menu);

            // passage en objets et enregistrements bdd
            $menuObject = new Menu();

            foreach ($menu as $key => $value) {
                $repository = $doctrine->getRepository(Recipe::class);
                $recipe = $repository->find($key);
                $menuObject->addRecipe($recipe);
            }

            $menuObject->setUser($user);

            $objectives = $form->getData();
            $objectives->setUser($user);

            $em = $doctrine->getManager();
            $em->persist($objectives);
            $em->persist($menuObject);

            $em->flush();

            // serialization and response
            $encoder = [new JsonEncoder(new JsonEncode(JSON_UNESCAPED_SLASHES))];
            $serializer = new Serializer([$menuNormalizer], $encoder);

            $context['metadata'] = [
                'status' => 200,
                'message' => 'Menu généré avec succès.',
                'coutMenu' => $total
            ];

            $data = $serializer->serialize($menuObject, 'json', $context);

            return new Response($data, 200, ['Content-Type' => 'application/json']);
        } else {
            return $this->json("raté");
        }
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
