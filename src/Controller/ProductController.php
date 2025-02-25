<?php

namespace App\Controller;

use App\DTO\ProductCreationDTO;
use App\DTO\ProductFilterDTO;
use App\DTO\ValidatableDTO;
use App\Exception\ValidationException;
use App\Service\CacheService;
use App\Service\ProductService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api')]
final class ProductController extends AbstractController
{
    #[Route('/products', name: 'list_products', methods: ['GET'])]
    public function index(
        Request            $request,
        ValidatorInterface $validator,
        CacheService       $cacheService,
    ): JsonResponse
    {
        $productFilterDTO = new ProductFilterDTO($request->query->all());
        $this->validateDTO($productFilterDTO, $validator);

        $paginatedProducts = $cacheService->fetchPaginatedProducts($productFilterDTO);

        $response = $this->buildJsonResponse($productFilterDTO, $paginatedProducts);

        return new JsonResponse($response, JsonResponse::HTTP_OK);
    }

    protected function validateDTO(ValidatableDTO $validatableDTO, ValidatorInterface $validator): void
    {
        $errors = $validator->validate($validatableDTO);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = [
                    'field' => $error->getPropertyPath(),
                    'message' => $error->getMessage(),
                ];
            }
            throw new ValidationException($errorMessages);
        }
    }

    private function buildJsonResponse(
        ProductFilterDTO $filterDTO,
        array            $paginatedProducts,
    ): array
    {
        $currentPage = $filterDTO->page;
        $itemsPerPage = $filterDTO->limit;
        $totalItems = $paginatedProducts['total'];
        $totalPages = (int)ceil($totalItems / $itemsPerPage);

        return [
            'meta' => [
                'current_page' => $currentPage,
                'limit' => $itemsPerPage,
                'total_items' => $totalItems,
                'total_pages' => $totalPages,
            ],
            'data' => array_map(function ($item) {
                return json_decode($item);
            }, $paginatedProducts['items']),
        ];
    }

    #[Route('/products/report', name: 'report_products', methods: ['GET'])]
    public function report(
        Request            $request,
        ValidatorInterface $validator,
        CacheService       $cacheService,
    ): Response
    {
        $productFilterDTO = new ProductFilterDTO($request->query->all());
        $this->validateDTO($productFilterDTO, $validator);

        $productsData = $cacheService->fetchPaginatedProducts($productFilterDTO);

        return $this->streamCsvResponse(
            $productsData['items'],
            'products_report.csv',
            'text/csv; charset=UTF-8'
        );
    }

    private function streamCsvResponse(array $products, string $filename, string $contentType): StreamedResponse
    {
        $csvFields = ['id', 'name', 'price', 'currency.code', 'category.name'];

        $response = new StreamedResponse(function () use ($products, $csvFields) {
            $output = fopen('php://output', 'w');
            foreach ($products as $product) {
                $row = $this->transformJsonToCsvRowAssoc($product, $csvFields);
                fputcsv($output, $row);
            }
            fclose($output);
        });

        $response->headers->set('Content-Type', $contentType);
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

        return $response;
    }

    private function transformJsonToCsvRowAssoc(string $json, array $fields): array
    {
        $data = json_decode($json, true);

        return array_map(function ($field) use ($data) {
            return $this->resolveNestedKey($data, $field);
        }, $fields);
    }

    private function resolveNestedKey(array $data, string $key)
    {
        $keys = explode('.', $key);
        $value = $data;

        foreach ($keys as $part) {
            if (!is_array($value) || !array_key_exists($part, $value)) {
                return '';
            }
            $value = $value[$part];
        }

        return $value;
    }

    #[Route('/products', name: 'add_product', methods: ['POST'])]
    public function add(
        Request             $request,
        ValidatorInterface  $validator,
        ProductService      $productService,
        SerializerInterface $serializer,
        CacheService        $cacheService,
    ): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        if ($requestData === null) {
            return new JsonResponse(['error' => 'Invalid JSON'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $productCreationDTO = new ProductCreationDTO($requestData);

        $this->validateDTO($productCreationDTO, $validator);

        $product = $productService->createProduct($productCreationDTO);

        $serializationGroup = ['groups' => 'product:create'];
        $serializedProduct = $serializer->serialize($product, 'json', $serializationGroup);

        $cacheService->clearCacheFilterEntries();
        $cacheService->setCacheFilterProduct($product);

        return new JsonResponse(['message' => 'Product created successfully',
            'data' => json_decode($serializedProduct)], JsonResponse::HTTP_CREATED);
    }
}
