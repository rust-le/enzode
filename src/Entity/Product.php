<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: 'App\Repository\ProductRepository')]
#[ORM\Table(name: "products")]
class Product
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: 'Ramsey\Uuid\Doctrine\UuidGenerator')]
    #[Groups(["product:list", "product:create"])]
    private string $id;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    #[Groups(["product:list"])]
    private string $name;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: "decimal", precision: 10, scale: 2, nullable: false)]
    #[Groups(["product:list"])]
    private float $price;

    #[ORM\Column(type: "datetime_immutable", nullable: false)]
    #[Groups(["product:list"])]
    private DateTimeImmutable $createdAt;

    #[ORM\ManyToOne(targetEntity: Currency::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["product:list"])]
    private Currency $currency;

    #[ORM\ManyToOne(targetEntity: Category::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["product:list"])]
    private Category $category;

    #[ORM\OneToMany(targetEntity: ProductAttribute::class, mappedBy: "product", cascade: ["persist", "remove"], fetch: "LAZY")]
    #[Groups(["product:list"])]
    private Collection $productAttributes;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->productAttributes = new ArrayCollection();
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getProductAttributes(): Collection
    {
        return $this->productAttributes;
    }

    public function setProductAttributes(Collection $productAttributes): void
    {
        $this->productAttributes = $productAttributes;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): void
    {
        $this->category = $category;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function setCurrency(Currency $currency): void
    {
        $this->currency = $currency;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getId(): string
    {
        return $this->id;
    }
}


