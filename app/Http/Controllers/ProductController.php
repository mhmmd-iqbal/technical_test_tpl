<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProductController extends Controller
{
    // Display a listing of the products
    public function index(Request $request)
    {
        if (Gate::denies('view-products')) {
            abort(403);
        }

        $query = Product::query();

        // Handle search functionality
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('price', 'like', "%{$search}%")
                    ->orWhereHas('categories', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Handle category filtering
        if ($request->filled('categories')) {
            $categories = $request->input('categories');
            $query->whereHas('categories', function ($q) use ($categories) {
                $q->whereIn('name', $categories);
            });
        }

        $products = $query->paginate(10);
        $allCategories = Category::all();

        return view('products.index', compact('products', 'allCategories'));
    }


    // Show the form for creating a new product
    public function create()
    {
        if (Gate::denies('manage-products')) {
            abort(403);
        }

        $allCategories = Category::all();
        return view('products.create', compact('allCategories'));
    }

    // Store a newly created product in the database
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'categories' => 'required|array',
            'categories.*' => 'string',
            'image' => 'nullable|image|max:2048',
        ]);

        $product = new Product($request->only(['name', 'description', 'price']));

        if ($request->hasFile('image')) {
            $product->image = $request->file('image')->store('public/images');
        }

        $product->save();

        $categories = $this->processCategories($request->input('categories'));
        $product->categories()->sync($categories);

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    // Show the form for editing the specified product
    public function edit(Product $product)
    {
        if (Gate::denies('manage-products')) {
            abort(403);
        }

        $allCategories = Category::all();
        return view('products.edit', compact('product', 'allCategories'));
    }

    // Update the specified product in the database
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'categories' => 'required|array',
            'categories.*' => 'string',
            'image' => 'nullable|image|max:2048',
        ]);

        $product->fill($request->only(['name', 'description', 'price']));

        if ($request->hasFile('image')) {
            $product->image = $request->file('image')->store('public/images');
        }

        $product->save();

        $categories = $this->processCategories($request->input('categories'));
        $product->categories()->sync($categories);

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    // Remove the specified product from the database
    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }

    protected function processCategories($categories)
    {
        return collect($categories)->map(function ($category) {
            return Category::firstOrCreate(['name' => $category])->id;
        });
    }
}
