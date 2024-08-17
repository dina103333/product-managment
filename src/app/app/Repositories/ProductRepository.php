<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository implements ProductRepositoryInterface
{
    public function create(array $attributes): Product
    {
        return Product::create($attributes);
    }

    public function findById(int $id): ?Product
    {
        return Product::find($id);
    }

    public function update(Product $product, array $attributes): bool
    {
        return $product->update($attributes);
    }

    public function delete(Product $product): bool
    {
        return $product->delete();
    }
}
