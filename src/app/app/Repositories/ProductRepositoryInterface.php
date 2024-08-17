<?php

namespace App\Repositories;

use App\Models\Product;

interface ProductRepositoryInterface
{
    public function create(array $attributes): Product;
    public function findById(int $id): ?Product;
    public function update(Product $product, array $attributes): bool;
    public function delete(Product $product): bool;
}