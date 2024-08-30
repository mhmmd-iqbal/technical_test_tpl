<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use Illuminate\Http\Request;

class ProductService
{
    protected $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function index(Request $request)
    {
        return $this->productRepository->getProducts(
            $request->input('search'),
            $request->input('categories', [])
        );
    }

    public function createProduct(array $data)
    {
        $product = $this->productRepository->createProduct($data);

        $this->productRepository->syncCategories($product, $data['categories']);

        return $product;
    }

    public function getProduct($id)
    {
        return $this->productRepository->getProduct($id);
    }

    public function updateProduct($product, array $data)
    {
        $this->productRepository->updateProduct($product, $data);

        $this->productRepository->syncCategories($product, $data['categories']);

        return $product;
    }

    public function deleteProduct($product)
    {
        return $this->productRepository->deleteProduct($product);
    }

    public function getAllCategories()
    {
        return $this->productRepository->getAllCategories();
    }
}
