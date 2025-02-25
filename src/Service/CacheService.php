<?php

namespace App\Service;

use App\DTO\ProductFilterDTO;
use App\Entity\Product;

interface CacheService
{
    public function fetchPaginatedProducts(ProductFilterDTO $productFilterDTO): array;

    public function clearCacheFilterEntries(): void;

    public function setCacheFilterProduct(Product $product): void;
}
