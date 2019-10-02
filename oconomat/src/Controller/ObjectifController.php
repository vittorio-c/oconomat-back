<?php

namespace App\Controller;


use App\Controller\RecipeController;
use App\Entity\User;
use App\Entity\Menu;
use App\Entity\Objectif;
use App\Entity\Recipe;
use App\Form\ObjectifType;
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
     * @Route(
     *      "/menu/generate",
     *      name="generate_menu",
     *      methods="POST",
     * )
     */
    public function generateMenu(Request $request)
    {

        //$em = $this->getDoctrine()->getRepository(Recipe::class);
        //$totalPrice = $em->getRecipieTotalPrice(3);
        //dump($totalPrice);
        //exit;

        $form = $this->createForm(ObjectifType::class);

        $data = json_decode($request->getContent(), true);

        $form->submit($data);

        if ($form->isValid()) {
            $objectives = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($objectives);
            $em->flush();
            //return $this->json('nouvel objectif créé');
        }


        // j'ai le budget, et il est enregistré en bdd avec l'utilisateur relié
        // objectif : 
        //      - générer une liste de recette 
        //      - qui respecte le budget donné
        //      - l'associser à un nouveau menu
        // requis :
        //      - connaître le prix total par recette
        //      - générer pour lee moment 21 recettes (sans les classer par type)
        //      - il faut que la somme du prix de ces 21 recettes ne dépasse pas le budget
        //      - je peux les ajouter une par une pour le moment jusqu'à en avoir 21
        //      - puis comparer la somme avec le budget : si supérieur, tu enlève la plus chère
        //      - et tu restes
        //
        // au moment de la requete bdd, générer une colonne calculée pour le prix de la recette
        //
        //
    }
}
