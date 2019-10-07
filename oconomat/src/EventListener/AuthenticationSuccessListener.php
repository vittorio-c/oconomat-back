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

            $event->setData([
                'userData' => [
                    'id' => $user->getId(),
                    'firstname' => $user->getFirstname(),
                    'lastname' => $user->getLastname(),
                    'budget' => $user->getObjectifs()[0]->getBudget(),
                ],
                'payload' => $event->getData(),
            ]);

            return;
        }


        //$event->setData($data);



        //return $event;
    }
}