<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ProductFilterDTO implements ValidatableDTO
{
    #[Assert\Type('string')]
    #[Assert\Length(max: 255)]
    public ?string $name = null;

    #[Assert\Type('string')]
    #[Assert\Length(max: 255)]
    public ?string $category = null;

    #[Assert\Type('numeric')]
    #[Assert\GreaterThanOrEqual(0)]
    public ?float $price_min = null;

    #[Assert\Type('numeric')]
    #[Assert\GreaterThanOrEqual(0)]
    public ?float $price_max = null;

    #[Assert\Type('integer')]
    #[Assert\GreaterThanOrEqual(1)]
    public int $page = 1;

    #[Assert\Type('integer')]
    #[Assert\Range(min: 1, max: 100)]
    public int $limit = 10;

    public function __construct(array $data)
    {
        $this->name = $data['name'] ?? null;
        $this->category = $data['category'] ?? null;
        $this->price_min = isset($data['price_min']) ? (float)$data['price_min'] : null;
        $this->price_max = isset($data['price_max']) ? (float)$data['price_max'] : null;
        $this->page = isset($data['page']) ? (int)$data['page'] : 1;
        $this->limit = isset($data['limit']) ? (int)$data['limit'] : 10;

    }
}
