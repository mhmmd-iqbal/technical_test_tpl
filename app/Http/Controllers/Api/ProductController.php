<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use OpenApi\Annotations as OA;

class ProductController extends Controller
{
    protected $productService;
    protected $products;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
        $this->products = new Product();
    }

    public function index(Request $request)
    {
        if (Gate::denies('view-products')) {
            abort(403);
        }

        $products = $this->productService->index($request);
        return response()->json([
            'message' => 'Products retrieved successfully',
            'data' => $products
        ], 200);
    }

    public function store(StoreProductRequest $request)
    {
        if (Gate::denies('manage-products')) {
            abort(403);
        }

        $data = $request->only(['name', 'description', 'price']);
        
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('public/images');
        }

        $data['categories'] = $request->input('categories');

        $this->productService->createProduct($data);

        return response()->json([
            'message' => 'Product created successfully',
            'data' => $data
        ], 201);
    }

    public function show($id)
    {
        if (Gate::denies('view-products')) {
            abort(403);
        }

        $product = $this->productService->getProduct($id);

        return response()->json([
            'message' => 'Product retrieved successfully',
            'data' => $product
        ], 200);
    }

    public function update(UpdateProductRequest $request, $id)
    {
        if (Gate::denies('manage-products')) {
            abort(403);
        }

        $data = $request->only(['name', 'description', 'price']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('public/images');
        }

        $data['categories'] = $request->input('categories');

        $this->productService->updateProduct($this->products->find($id), $data);

        return response()->json([
            'message' => 'Product updated successfully',
            'data' => $data
        ], 200);
    }


    public function destroy($id)
    {
        if (Gate::denies('manage-products')) {
            abort(403);
        }

        $this->productService->deleteProduct($this->products->find($id));

        return response()->json([
            'message' => 'Product deleted successfully',
        ], 200);
    }
}

