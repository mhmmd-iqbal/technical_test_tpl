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

/**
 * @OA\Tag(
 *     name="Products",
 *     description="API Endpoints for managing products"
 * )
 */
class ProductController extends Controller
{
    protected $productService;
    protected $products;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
        $this->products = new Product();
    }

    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="Get list of products",
     *     tags={"Products"},
     *     security={{"passport":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Products retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Products retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="first_page_url", type="string", example="http://localhost:8000/api/products?page=1"),
     *                 @OA\Property(property="from", type="integer", nullable=true),
     *                 @OA\Property(property="last_page", type="integer", example=1),
     *                 @OA\Property(property="last_page_url", type="string", example="http://localhost:8000/api/products?page=1"),
     *                 @OA\Property(property="links", type="array", @OA\Items(type="object",
     *                     @OA\Property(property="url", type="string", nullable=true),
     *                     @OA\Property(property="label", type="string", example="&laquo; Previous"),
     *                     @OA\Property(property="active", type="boolean", example=false)
     *                 )),
     *                 @OA\Property(property="next_page_url", type="string", nullable=true),
     *                 @OA\Property(property="path", type="string", example="http://localhost:8000/api/products"),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="prev_page_url", type="string", nullable=true),
     *                 @OA\Property(property="to", type="integer", nullable=true),
     *                 @OA\Property(property="total", type="integer", example=0)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/products",
     *     summary="Create a new product",
     *     tags={"Products"},
     *     security={{"passport":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "description", "price"},
     *             @OA\Property(property="name", type="string", example="Product Name"),
     *             @OA\Property(property="description", type="string", example="Product Description"),
     *             @OA\Property(property="price", type="number", example=99.99),
     *             @OA\Property(property="categories", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="image", type="string", format="binary")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Product created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product created successfully."),
     *             @OA\Property(property="data", type="object", 
     *                 @OA\Property(property="name", type="string", example="Product Name"),
     *                 @OA\Property(property="description", type="string", example="Product Description"),
     *                 @OA\Property(property="price", type="number", example=99.99),
     *                 @OA\Property(property="categories", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="image", type="string", example="path/to/image.jpg")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/products/{id}",
     *     summary="Get a specific product by ID",
     *     tags={"Products"},
     *     security={{"passport":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Product ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product retrieved successfully"),
     *             @OA\Property(property="data", type="object", 
     *                 @OA\Property(property="name", type="string", example="Product Name"),
     *                 @OA\Property(property="description", type="string", example="Product Description"),
     *                 @OA\Property(property="price", type="number", example=99.99),
     *                 @OA\Property(property="categories", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="image", type="string", example="path/to/image.jpg")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Product not found"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
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

    /**
     * @OA\Put(
     *     path="/api/products/{id}",
     *     summary="Update an existing product",
     *     tags={"Products"},
     *     security={{"passport":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Product ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "description", "price"},
     *             @OA\Property(property="name", type="string", example="Updated Product Name"),
     *             @OA\Property(property="description", type="string", example="Updated Product Description"),
     *             @OA\Property(property="price", type="number", example=99.99),
     *             @OA\Property(property="categories", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="image", type="string", format="binary")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product updated successfully."),
     *             @OA\Property(property="data", type="object", 
     *                 @OA\Property(property="name", type="string", example="Updated Product Name"),
     *                 @OA\Property(property="description", type="string", example="Updated Product Description"),
     *                 @OA\Property(property="price", type="number", example=99.99),
     *                 @OA\Property(property="categories", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="image", type="string", example="path/to/image.jpg")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
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


    /**
     * @OA\Delete(
     *     path="/api/products/{id}",
     *     summary="Delete a product",
     *     tags={"Products"},
     *     security={{"passport":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Product ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product deleted successfully.")
     *         )
     *     ),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
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
