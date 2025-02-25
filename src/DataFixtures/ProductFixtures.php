<?php

namespace App\DataFixtures;

use App\DTO\ProductCreationDTO;
use App\Factory\ProductFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;

class ProductFixtures extends Fixture
{
    private const ATTRIBUTE_CODES = ['COL', 'PRI', 'AUT', 'HEI', 'LEN', 'WEI', 'GEN', 'SIZ', 'EAN', 'ISB'];
    private ProductFactory $productFactory;

    public function __construct(ProductFactory $productFactory)
    {
        $this->productFactory = $productFactory;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = FakerFactory::create('en_US');

        for ($i = 0; $i < 1000; $i++) {

            $dateTime = $faker->dateTime;
            $dateString = $dateTime->format('Y-m-d H:i:s');

            $productCreationDTO = new ProductCreationDTO(
                [
                    'name' => $faker->sentence(3) . ' ' . $i,
                    'createdAt' => $dateString,
                    'description' => $faker->sentence(10),
                    'price' => $faker->randomFloat(2, 10, 500),
                    'currency' => ['code' => $faker->currencyCode],
                    'category' => ['name' => $faker->word],
                    'productAttributes' => [
                        [
                            'attribute' => [
                                'code' => $faker->randomElement(self::ATTRIBUTE_CODES)
                            ], 'value' => $faker->word
                        ]
                    ]
                ]
            );
            $this->productFactory->createProduct($productCreationDTO);
            $manager->flush();
        }
    }
}
