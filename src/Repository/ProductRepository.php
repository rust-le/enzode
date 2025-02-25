<?php

namespace App\Repository;

use App\DTO\ProductFilterDTO;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findByFiltersWithPagination(
        ProductFilterDTO $productFilterDTO,
    ): array
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.category', 'c')
            ->addSelect('c');

        if ($productFilterDTO->name) {
            $qb->andWhere('p.name LIKE :name')
                ->setParameter('name', '%' . $productFilterDTO->name . '%');
        }

        if ($productFilterDTO->category) {
            $qb->andWhere('c.name = :category')
                ->setParameter('category', $productFilterDTO->category);
        }

        if ($productFilterDTO->price_min !== null) {
            $qb->andWhere('p.price >= :price_min')
                ->setParameter('price_min', $productFilterDTO->price_min);
        }

        if ($productFilterDTO->price_max !== null) {
            $qb->andWhere('p.price <= :price_max')
                ->setParameter('price_max', $productFilterDTO->price_max);
        }

        $total = (clone $qb)->select('COUNT(p.id)')->getQuery()->getSingleScalarResult();

        $offset = ($productFilterDTO->page - 1) * $productFilterDTO->limit;
        $qb->setFirstResult($offset)
            ->setMaxResults($productFilterDTO->limit);

        $items = $qb->getQuery()->getResult();

        return [
            'items' => $items,
            'total' => (int)$total,
        ];
    }
}
