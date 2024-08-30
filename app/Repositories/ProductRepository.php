<?php

namespace App\Repositories;

use App\Models\Product;
use App\Models\Category;

class ProductRepository
{
    public function getProducts($search = null, $categories = [])
    {
        $query = Product::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('price', 'like', "%{$search}%")
                    ->orWhereHas('categories', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if (!empty($categories)) {
            $query->whereHas('categories', function ($q) use ($categories) {
                $q->whereIn('name', $categories);
            });
        }

        return $query->paginate(10);
    }

    public function getProduct($id)
    {
        return Product::find($id);
    }

    public function createProduct(array $data)
    {
        return Product::create($data);
    }

    public function updateProduct(Product $product, array $data)
    {
        $product->fill($data);
        $product->save();

        return $product;
    }

    public function deleteProduct(Product $product)
    {
        $product->delete();
    }

    public function syncCategories(Product $product, array $categories)
    {
        $categoryIds = collect($categories)->map(function ($category) {
            return Category::firstOrCreate(['name' => $category])->id;
        });

        $product->categories()->sync($categoryIds);
    }

    public function getAllCategories()
    {
        return Category::all();
    }
}
