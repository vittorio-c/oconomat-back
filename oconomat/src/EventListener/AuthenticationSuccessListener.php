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
        $user = $event->getUser();


        if (!$user instanceof UserInterface) {

            $budgetList =  $user->getObjectifs();

            $budget = null;

            if (!$budgetList->isEmpty())
            {
                $budget =  $budgetList->last()->getBudget();
            }

            $event->setData([
                'id' => $user->getId(),
                'budget' => $budget,
                'firstname' => $user->getFirstname(),
                'lastname' => $user->getLastname(),
                'payload' => $event->getData(),
            ]);

            return;
        }

    }
}