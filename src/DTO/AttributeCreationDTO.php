<?php
namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class AttributeCreationDTO
{
    #[Assert\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 3)]
    public string $code;

    public function __construct(array $data)
    {
        $this->code = $data['code'];
    }
}
