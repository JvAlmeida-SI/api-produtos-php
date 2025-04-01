<?php

namespace Tests\Unit\Services;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\Products\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;
use Illuminate\Support\Collection;

class ProductServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $productRepository;
    protected $productService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productRepository = Mockery::mock(ProductRepositoryInterface::class);
        $this->productService = new ProductService($this->productRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_create_product()
    {
        $productData = [
            'name' => 'Test Product',
            'user_id' => 1,
        ];

        $this->productRepository->shouldReceive('create')
            ->once()
            ->with($productData)
            ->andReturn(new Product($productData));

        $product = $this->productService->createProduct($productData, 1);

        $this->assertEquals('Test Product', $product->name);
    }

    public function test_get_filtered_products()
    {
        // 1. Preparar os dados de teste
        $filters = ['name' => 'Test'];
        
        // Criar um mock de Product
        $mockProduct = Mockery::mock(Product::class);
        $mockProduct->shouldReceive('getAttribute')->with('name')->andReturn('Test Product');
        
        // Criar uma coleção com o mock
        $expectedResult = new \Illuminate\Database\Eloquent\Collection([$mockProduct]);

        // 2. Configurar o mock do repositório
        $this->productRepository->shouldReceive('all')
            ->once()
            ->with($filters)
            ->andReturn($expectedResult);

        // 3. Executar o método
        $result = $this->productService->getFilteredProducts($filters);

        // 4. Verificações
        $this->assertCount(1, $result);
        $this->assertEquals('Test Product', $result->first()->name);
    }
}