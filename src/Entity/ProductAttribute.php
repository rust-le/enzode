<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity]
#[ORM\Table(name: "product_attributes")]
class ProductAttribute
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: 'Ramsey\Uuid\Doctrine\UuidGenerator')]
    private string $id;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Product $product;

    #[ORM\ManyToOne(targetEntity: Attribute::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["product:list"])]
    private Attribute $attribute;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    #[Groups(["product:list"])]
    private string $value;

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    public function getAttribute(): Attribute
    {
        return $this->attribute;
    }

    public function setAttribute(Attribute $attribute): void
    {
        $this->attribute = $attribute;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): void
    {
        $this->product = $product;
    }

    public function getId(): string
    {
        return $this->id;
    }
}
