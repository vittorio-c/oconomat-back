<?php

namespace App\Service;

use Symfony\Component\Security\Core\Security;

class CheckOwnership 
{
    private $security;
    private $data;
    private $code;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function check($entity)
    {
        $currentUser = $this->security->getUser();
        $user = $entity->getUser();
        if ($currentUser === $user) {
            return true;
        } else {
            $this->data = [
                'code status' => 403,
                'message' => 'The connected user does not have sufficiant privileges to access this ressource.'
            ];

            $this->code = 403;
            return false;
        }
    }

    public function getData()
    {
        return $this->data;
    }

    public function getCode()
    {
        return $this->code;
    }
}

