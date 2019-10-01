<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Menu;
use App\Entity\Objectif;
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

        $form = $this->createForm(ObjectifType::class);

        $data = json_decode($request->getContent(), true);

        $form->submit($data);

        if ($form->isValid()) {
            $objectives = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($objectives);
            $em->flush();
            return $this->json('nouvel objectif créé');
        }
    }
}
