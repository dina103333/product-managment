<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Requests\Api\Product\ProductRequest;
use App\Http\Requests\Api\Product\UpdateProductRequest;
use App\Http\Resources\Api\ProductResource;
use App\Traits\ApiResponse;
use App\Repositories\ProductRepositoryInterface;


class ProductController extends Controller
{
    use ApiResponse;
    protected $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function store(ProductRequest $request)
    {
        $product = $this->productRepository->create($request->all());
        return $this->successResponse(ProductResource::make($product), 'Product created successfully', 201);
    }

    public function show($id)
    {
        $product = $this->productRepository->findById($id);

        if (!$product) {
            return $this->notFoundResponse('Product not found.');
        }

        return $this->successResponse(ProductResource::make($product), 'Data retrieved successfully', 201);
    }

    public function update(UpdateProductRequest $request, $id)
    {
        $product = $this->productRepository->findById($id);

        if (!$product) {
            return $this->notFoundResponse('Product not found.');
        }

        $this->productRepository->update($product, $request->all());

        return $this->successResponse(ProductResource::make($product), 'Data updated successfully', 201);
    }

    public function destroy($id)
    {
        $product = $this->productRepository->findById($id);

        if (!$product) {
            return $this->notFoundResponse('Product not found.');
        }

        $this->productRepository->delete($product);

        return $this->successResponse(null, 'Product deleted successfully', 200);
    }
}
