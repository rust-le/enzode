<?php

namespace App\Factory;

use App\DTO\ProductCreationDTO;
use App\Entity\Attribute;
use App\Entity\Category;
use App\Entity\Currency;
use App\Entity\Product;
use App\Entity\ProductAttribute;
use Doctrine\ORM\EntityManagerInterface;

class ProductFactory
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createProduct(ProductCreationDTO $productCreationDTO): Product
    {
        $currency = $this->entityManager->getRepository(Currency::class)->findOneBy(['code' => $productCreationDTO->currency->code]);
        if (!$currency) {
            $currency = new Currency();
            $currency->setCode($productCreationDTO->currency->code);
            $this->entityManager->persist($currency);
        }

        $category = $this->entityManager->getRepository(Category::class)->findOneBy(['name' => $productCreationDTO->category->name]);
        if (!$category) {
            $category = new Category();
            $category->setName($productCreationDTO->category->name);
            $this->entityManager->persist($category);
        }

        $product = new Product();
        $product->setName($productCreationDTO->name);
        $product->setDescription($productCreationDTO->description);
        $product->setPrice($productCreationDTO->price);
        $product->setCurrency($currency);
        $product->setCategory($category);
        $this->entityManager->persist($product);

        foreach ($productCreationDTO->productAttributes as $productAttribute) {
            $attributeCode = $productAttribute->attribute->code;
            $attributeValue = $productAttribute->value;

            $attribute = $this->entityManager->getRepository(Attribute::class)->findOneBy(['code' => $attributeCode]);
            if (!$attribute) {
                $attribute = new Attribute();
                $attribute->setCode($attributeCode);
                $this->entityManager->persist($attribute);
            }
            $productAttributeEntity = new ProductAttribute();

            $productAttributeEntity->setProduct($product);
            $productAttributeEntity->setAttribute($attribute);
            $productAttributeEntity->setValue($attributeValue);
            $this->entityManager->persist($productAttributeEntity);
        }

        return $product;
    }
}
