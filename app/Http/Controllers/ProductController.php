<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\ProductService;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }


    public function index(Request $request)
    {
        if (Gate::denies('view-products')) {
            abort(403);
        }

        $products = $this->productService->index($request);
        $allCategories = $this->productService->getAllCategories();

        return view('products.index', compact('products', 'allCategories'));
    }

    public function create()
    {
        if (Gate::denies('manage-products')) {
            abort(403);
        }

        $allCategories = $this->productService->getAllCategories();
        return view('products.create', compact('allCategories'));
    }

    public function store(StoreProductRequest $request)
    {
        $data = $request->only(['name', 'description', 'price']);
        
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('public/images');
        }

        $data['categories'] = $request->input('categories');

        $this->productService->createProduct($data);

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        if (Gate::denies('manage-products')) {
            abort(403);
        }

        $allCategories = $this->productService->getAllCategories();
        return view('products.edit', compact('product', 'allCategories'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $request->only(['name', 'description', 'price']);
        
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('public/images');
        }

        $data['categories'] = $request->input('categories');

        $this->productService->updateProduct($product, $data);

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        if (Gate::denies('manage-products')) {
            abort(403);
        }

        $this->productService->deleteProduct($product);

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}
