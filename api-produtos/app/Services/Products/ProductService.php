<?php

namespace App\Services\Products;

use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ProductService
{
    protected $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function getFilteredProducts(array $filters = []): Collection
    {
        return $this->productRepository->all($filters);
    }

    public function createProduct(array $data, $userId)
    {
        $data['user_id'] = $userId;
        return $this->productRepository->create($data);
    }

    public function getProductById($id)
    {
        return $this->productRepository->find($id);
    }

    public function updateProduct(array $data, $id)
    {
        $product = Product::findOrFail($id);
        
        // Não atualiza se os preços forem inválidos
        if ($data['sale_price'] <= $data['purchase_price']) {
            throw new \InvalidArgumentException('Sale price must be greater than purchase price');
        }
        
        $product->update($data);
        return $product;
    }

    public function deleteProduct($id)
    {
        return $this->productRepository->delete($id);
    }
}