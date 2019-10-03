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


        $data['data'] = array(
            'roles' => $user->getRoles(),
        );

        //$event->setData($data);

        $event->setData([
            'userId' => $user->getId(),
            'payload' => $event->getData(),
        ]);


        //return $event;
    }
}