<?php

namespace Tests\Feature\Products;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductListingTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_products()
    {
        Product::factory()->count(3)->create();

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_products_can_be_filtered_by_name()
    {
        // Criar produtos de teste
        $product1 = Product::factory()->create(['name' => 'Notebook Dell']);
        $product2 = Product::factory()->create(['name' => 'Mouse Logitech']);
        $product3 = Product::factory()->create(['name' => 'Teclado Microsoft']);
        
        // Testar filtro por "tec" (deve retornar Mouse e Teclado)
        $response = $this->getJson('/api/products?name=tec');
        
        $response->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['name' => 'Mouse Logitech'])
            ->assertJsonFragment(['name' => 'Teclado Microsoft'])
            ->assertJsonMissing(['name' => 'Notebook Dell']);
    }

    public function test_products_can_be_filtered_by_category()
    {
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();

        Product::factory()->count(2)->create(['category_id' => $category1->id]);
        Product::factory()->create(['category_id' => $category2->id]);

        $response = $this->getJson('/api/products?category_id=' . $category1->id);

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_products_can_be_filtered_by_price_range()
    {
        Product::factory()->create(['sale_price' => 50]);
        Product::factory()->create(['sale_price' => 100]);
        Product::factory()->create(['sale_price' => 150]);

        $response = $this->getJson('/api/products?min_price=75&max_price=125');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id', 'name', 'description', 
                        'purchase_price', 'sale_price',
                        'category_id', 'user_id'
                    ]
                ],
                'meta'
            ])
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['sale_price' => 100]);
    }

    public function test_user_can_view_single_product()
    {
        $product = Product::factory()->create();

        $response = $this->getJson('/api/products/' . $product->id);

        $response->assertStatus(200)
            ->assertJson(['id' => $product->id]);
    }
}