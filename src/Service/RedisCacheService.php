<?php

namespace App\Service;

use App\DTO\ProductFilterDTO;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Redis;
use Symfony\Component\Serializer\SerializerInterface;

class RedisCacheService implements CacheService
{
    private const CACHE_FILTER_PREFIX = 'QUERY:';
    private const CACHE_PRODUCT_PREFIX = 'PRODUCT:';
    private ProductRepository $productRepository;
    private Redis $redis;
    private SerializerInterface $serializer;

    public function __construct(
        ProductRepository   $productRepository,
        Redis               $redis,
        SerializerInterface $serializer
    )
    {
        $this->productRepository = $productRepository;
        $this->redis = $redis;
        $this->serializer = $serializer;
    }

    public function clearCacheFilterEntries(): void
    {
        $keys = $this->redis->keys(self::CACHE_FILTER_PREFIX . '*');
        if (!empty($keys)) {
            $this->redis->del($keys);
        }
    }

    public function setCacheFilterProduct(Product $product): void
    {
        $this->setCacheFilterProducts(['items' => [$product]]);
    }

    private function setCacheFilterProducts(array $paginatedNonCachedProducts): void
    {
        foreach ($paginatedNonCachedProducts['items'] as $product) {
            $serializationGroup = ['groups' => 'product:list'];
            $serializedProduct = $this->serializer->serialize($product, 'json', $serializationGroup);
            $this->redis->set(self::CACHE_PRODUCT_PREFIX . $product->getId(), $serializedProduct);
        }
    }

    public function fetchPaginatedProducts(ProductFilterDTO $productFilterDTO): array
    {
        $cacheFilterKey = $this->generateCacheFilterKey($productFilterDTO);

        if ($this->existCacheFilterKey($cacheFilterKey)) {
            return $this->getPaginatedCachedProducts($cacheFilterKey);
        }

        $paginatedNonCachedProducts = $this->productRepository->findByFiltersWithPagination($productFilterDTO);

        $this->setCacheFilterKey($cacheFilterKey, $paginatedNonCachedProducts);

        $this->setCacheFilterProducts($paginatedNonCachedProducts);

        $paginatedNonCachedProducts['items'] = $this->serializeProducts($paginatedNonCachedProducts['items']);

        return $paginatedNonCachedProducts;
    }

    private function generateCacheFilterKey(ProductFilterDTO $productFilterDTO): string
    {
        return self::CACHE_FILTER_PREFIX . md5(serialize($productFilterDTO));
    }

    private function existCacheFilterKey(string $cacheKey): bool
    {
        return $this->redis->exists($cacheKey);
    }

    private function getPaginatedCachedProducts(string $cacheFilterKey): array
    {
        $productIds = json_decode($this->redis->get($cacheFilterKey), true);
        $cacheProductKeys = array_map(
            fn($id) => self::CACHE_PRODUCT_PREFIX . $id,
            $productIds['items'] ?? []
        );

        $serializedProducts = $this->redis->mget($cacheProductKeys);
        if ($serializedProducts === false) {
            $serializedProducts = [];
        }

        $products = $this->deserializeCachedProducts($serializedProducts);

        return [
            'items' => $products,
            'total' => $productIds['total'],
        ];
    }

    private function deserializeCachedProducts(array $serializedProducts): array
    {
        return array_values(array_filter($serializedProducts, fn($product) => !is_null($product)));
    }

    private function setCacheFilterKey(string $cacheKey, array $paginatedProduct): void
    {
        $productIdList = $this->extractProductIds($paginatedProduct['items']);
        $this->redis->set($cacheKey, json_encode(
            [
                'items' => $productIdList,
                'total' => $paginatedProduct['total'],
            ]));
    }

    private function extractProductIds(array $productItems): array
    {
        return array_map(fn($item) => $item->getId(), $productItems);
    }

    private function serializeProducts(array $products): array
    {
        return array_map(fn($product) => $this->serializer->serialize(
            $product,
            'json',
            ['groups' => 'product:list']
        ), $products);
    }
}
