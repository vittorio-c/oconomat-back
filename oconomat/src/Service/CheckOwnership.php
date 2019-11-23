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

    public function check($menu)
    {
        $currentUser = $this->security->getUser();
        $user = $menu->getUser();
        if ($currentUser === $user) {
            return true;
        } else {
            $this->data = [
                'status' => 403,
                'message' => 'L\'utilisateur connecté n\'a pas les droits nécessaires pour accéder à cette ressource.'
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

