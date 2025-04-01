<?php

namespace Tests\Unit\Repositories;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Repositories\Eloquent\ProductRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new ProductRepository(new Product());
    }

    public function test_filter_products_by_name()
    {
        // Produtos que DEVEM ser encontrados
        $product1 = Product::factory()->create(['name' => 'Notebook Product']);
        $product2 = Product::factory()->create(['name' => 'Product Mouse']);
        
        // Produto que NÃO deve ser encontrado
        Product::factory()->create(['name' => 'Teclado']);
        
        $products = $this->repository->all(['name' => 'Product']);
        
        $this->assertCount(2, $products);
        $this->assertTrue($products->contains($product1));
        $this->assertTrue($products->contains($product2));
    }

    public function test_filter_products_by_category()
    {
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();

        Product::factory()->count(2)->create(['category_id' => $category1->id]);
        Product::factory()->create(['category_id' => $category2->id]);

        $products = $this->repository->all(['category_id' => $category1->id]);

        $this->assertCount(2, $products);
    }
}