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
                'userId' => $user->getId(),
                'payload' => $event->getData(),
            ]);

            return;
        }


        //$event->setData($data);



        //return $event;
    }
}