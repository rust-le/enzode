<?php

namespace App\Service;

use App\DTO\ProductCreationDTO;
use App\Entity\Product;
use App\Factory\ProductFactory;
use Doctrine\ORM\EntityManagerInterface;

class ProductService
{
    private EntityManagerInterface $entityManager;
    private ProductFactory $ProductFactory;

    public function __construct(EntityManagerInterface $entityManager, ProductFactory $ProductFactory)
    {
        $this->entityManager = $entityManager;
        $this->ProductFactory = $ProductFactory;
    }

    public function createProduct(ProductCreationDTO $productCreationDTO): Product
    {
        $product = $this->ProductFactory->createProduct($productCreationDTO);

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return $product;
    }
}
