<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;


class AuthenticationSuccessListener
{
    /**
     * @param AuthenticationSuccessEvent $event
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        $user = $event->getUser();


        if (!$user instanceof UserInterface) {

            $test =  $user->getObjectifs();

            $property = null;

            if (!$test->isEmpty())
            {
                $property =  $test->last()->getBudget();
            }

            $event->setData([
                'id' => $user->getId(),
                'budget' => $property,
                'firstname' => $user->getFirstname(),
                'lastname' => $user->getLastname(),
                'payload' => $event->getData(),
            ]);

            return;
        }


        //$event->setData($data);



        //return $event;
    }
}