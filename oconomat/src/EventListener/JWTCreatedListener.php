<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;


class JWTCreatedListener
{

    public function onJwtCreated(JWTCreatedEvent $event): void
    {
        // $user = $event->getUser();

        // $payload = $event->getData();
        // $payload['id'] = $user->getId();

        // //$event->setData($payload);

        // $event->setData([
        //     'payload' => $event->getData(),
        // ]);
    }

}