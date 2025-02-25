<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CategoryCreationDTO
{
    #[Assert\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $name;

    public function __construct(array $data)
    {
        $this->name = $data['name'];
    }
}
