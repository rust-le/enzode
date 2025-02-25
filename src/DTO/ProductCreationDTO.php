<?php

namespace App\DTO;

use DateTimeImmutable;
use DateTimeInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ProductCreationDTO implements ValidatableDTO
{
    #[Assert\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $name;

    #[Assert\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 1024)]
    public string $description;

    #[Assert\Type('float')]
    #[Assert\Positive]
    public float $price;

    #[Assert\Type('\DateTimeInterface')]
    #[Assert\NotBlank]
    public DateTimeInterface $createdAt;

    #[Assert\Valid]
    #[Assert\NotNull]
    public CurrencyCreationDTO $currency;

    #[Assert\Valid]
    #[Assert\NotNull]
    public CategoryCreationDTO $category;

    #[Assert\Valid]
    #[Assert\NotNull]
    public array $productAttributes;

    public function __construct(array $data)
    {
        $this->name = $data['name'];
        $this->description = $data['description'];
        $this->price = $data['price'];
        $this->createdAt = new DateTimeImmutable($data['createdAt']);
        $this->currency = new CurrencyCreationDTO($data['currency']);
        $this->category = new CategoryCreationDTO($data['category']);
        $this->productAttributes = array_map(
            fn($attribute) => new ProductAttributeCreationDTO($attribute),
            $data['productAttributes'] ?? []
        );
    }
}
