<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ObjectifDto
{
    /**
     * @Assert\Type("int")
     */
    private $budget;

    /**
     * @Assert\Type("bool")
     */
    private $vegetarian;

    /**
     * @Assert\Type("int")
     */
    private $userQuantity;

    public static function fromRequestData($data): self
    {
        $objectifDto= new self();

        $objectifDto->budget = $data['budget'] ?? 0;
        $objectifDto->vegetarian = $data['vegetarian'] ?? false;
        $objectifDto->userQuantity = $data['userQuantity'] ?? 1;

        return $objectifDto;
    }
}
