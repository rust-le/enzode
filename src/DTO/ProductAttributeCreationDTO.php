<?php

namespace App\DTO;

use InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;

class ProductAttributeCreationDTO
{
    #[Assert\Valid]
    #[Assert\NotNull]
    public AttributeCreationDTO $attribute;

    #[Assert\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $value;

    public function __construct(array $data)
    {
        if (!isset($data['attribute']) || !is_array($data['attribute'])) {
            throw new InvalidArgumentException('Attribute data is required and must be an array.');
        }

        $this->attribute = new AttributeCreationDTO($data['attribute']);
        $this->value = $data['value'];
    }
}
