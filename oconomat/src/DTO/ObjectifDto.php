<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ObjectifDto
{
    /**
     * @Assert\Type("float")
     */
    private $budget;

    public static function fromRequestData($data): self
    {
        $objectifDto= new self();
        $objectifDto->budget = $data['budget'] ?? 0;

        return $objectifDto;
    }
}
