<?php

namespace Tests\Feature\Products;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Configuração do JWT para testes
        config(['jwt.secret' => 'base64:W1g1Z0xPcWtFQnVzY2ZqT3RlU0hHd1V5SnJvWmJXeDc=']);
        
        $this->user = User::factory()->create();
        $this->token = JWTAuth::fromUser($this->user);
    }

    public function test_user_can_create_product()
    {
        $category = Category::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/products', [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'category_id' => $category->id,
            'purchase_price' => 100,
            'sale_price' => 150,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'name' => 'Test Product',
                'description' => 'Test Description',
                'purchase_price' => 100,
                'sale_price' => 150
            ]);
    }

    public function test_user_can_update_their_product()
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $category->id
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson('/api/products/' . $product->id, [
            'name' => 'Updated Product',
            'description' => 'Updated Description',
            'category_id' => $category->id,
            'purchase_price' => 200,
            'sale_price' => 300,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'name' => 'Updated Product',
                'description' => 'Updated Description',
                'purchase_price' => 200,
                'sale_price' => 300
            ]);
    }

    public function test_user_cannot_update_others_products()
    {
        $otherUser = User::factory()->create();
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'user_id' => $otherUser->id,
            'category_id' => $category->id,
            'purchase_price' => 100,
            'sale_price' => 150
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson('/api/products/' . $product->id, [
            'name' => 'Updated Product',
            'category_id' => $category->id,
            'purchase_price' => 100, 
            'sale_price' => 150
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => $product->name // Verifica que o nome não foi alterado
        ]);
    }

    public function test_user_can_delete_their_product()
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $category->id
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->deleteJson('/api/products/' . $product->id);

        $response->assertStatus(204);
        $this->assertSoftDeleted($product);
    }

    public function test_product_creation_requires_valid_data()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/products', [
            'name' => '',
            'purchase_price' => 'invalid',
            'sale_price' => 50
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'name', 
                'purchase_price',
                'category_id'
            ]);
    }
}