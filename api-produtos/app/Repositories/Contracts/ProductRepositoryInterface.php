<?php

namespace App\Repositories\Contracts;

interface ProductRepositoryInterface
{
    public function all(array $filters = []);
    public function create(array $data);
    public function update(array $data, $id);
    public function delete($id);
    public function find($id);
    public function findByUser($userId, array $filters = []);
}