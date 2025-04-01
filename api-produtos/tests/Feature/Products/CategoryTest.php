<?php

namespace Tests\Feature\Products;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_categories()
    {
        Category::factory()->count(3)->create();

        $response = $this->getJson('/api/categories');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'description']
                ],
                'meta' => ['total']
            ])
            ->assertJsonCount(3, 'data');
    }

    public function test_can_create_category()
    {
        $response = $this->postJson('/api/categories', [
            'name' => 'Test Category',
            'description' => 'Test Description',
        ]);

        $response->assertStatus(201)
            ->assertJson(['name' => 'Test Category']);
    }

    public function test_can_view_category()
    {
        $category = Category::factory()->create();

        $response = $this->getJson('/api/categories/' . $category->id);

        $response->assertStatus(200)
            ->assertJson(['id' => $category->id]);
    }

    public function test_can_update_category()
    {
        // Cria um usuário e obtém o token
        $user = User::factory()->create();
        $token = auth()->login($user);

        // Cria a categoria
        $category = Category::factory()->create();

        // Faz a requisição com autenticação
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/categories/' . $category->id, [
            'name' => 'Updated Category',
        ]);

        // Verificações
        $response->assertStatus(200)
            ->assertJson(['name' => 'Updated Category']);
    }

    public function test_can_delete_category()
    {
        // Cria um usuário e obtém o token
        $user = User::factory()->create();
        $token = auth()->login($user);

        // Cria a categoria
        $category = Category::factory()->create();

        // Faz a requisição com autenticação
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson('/api/categories/' . $category->id);

        // Verificações
        $response->assertStatus(204);
        $this->assertSoftDeleted($category);
    }
}