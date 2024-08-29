<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_product()
    {
        // Arrange
        $user = User::factory()->create(['role' => 'admin']); // Create an admin user
        $this->actingAs($user); // Authenticate the user

        $category = Category::factory()->create();
        $productData = [
            'name' => 'Test Product',
            'description' => 'This is a test product',
            'price' => 99.99,
            'categories' => [$category->name],
        ];

        // Act
        $response = $this->post(route('products.store'), $productData);

        // Assert
        $response->assertRedirect(route('products.index'));
        $this->assertDatabaseHas('products', ['name' => 'Test Product']);
        $product = Product::where('name', 'Test Product')->first();
        $this->assertTrue($product->categories->contains($category));
    }

    /** @test */
    public function it_can_read_a_product()
    {
        // Arrange
        $user = User::factory()->create(['role' => 'admin']); // Create an admin user
        $this->actingAs($user); // Authenticate the user

        $product = Product::factory()->create();

        // Act
        $response = $this->get(route('products.index'));

        // Assert
        $response->assertStatus(200);
        $response->assertSee($product->name);
    }

    /** @test */
    public function it_can_update_a_product()
    {
        // Arrange
        $user = User::factory()->create(['role' => 'admin']); // Ensure the user is admin
        $this->actingAs($user); // Authenticate as this user

        $product = Product::factory()->create();
        $updatedData = [
            'name' => 'Updated Product Name',
            'description' => 'Updated description',
            'price' => 199.99,
            'image' => 'https://example.com/image.jpg',
            'categories' => $product->categories->pluck('name')->toArray(),
        ];

        // Act
        $response = $this->put(route('products.update', $product), $updatedData);

        // Assert
        $response->assertRedirect(route('products.index')); // Make sure it redirects to the index route
        $this->assertDatabaseHas('products', ['name' => 'Updated Product Name']); // Check that the database has the updated product
    }


    /** @test */
    public function it_can_delete_a_product()
    {
        // Arrange
        $user = User::factory()->create(['role' => 'admin']); // Create an admin user
        $this->actingAs($user); // Authenticate the user

        $product = Product::factory()->create();

        // Act
        $response = $this->delete(route('products.destroy', $product));

        // Assert
        $response->assertRedirect(route('products.index'));
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
