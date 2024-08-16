<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Requests\Api\Product\ProductRequest;
use App\Http\Requests\Api\Product\UpdateProductRequest;
use App\Http\Resources\Api\ProductResource;
use App\Traits\ApiResponse;


class ProductController extends Controller
{
    use ApiResponse;

    public function store(ProductRequest $request)
    {
        $product = Product::create($request->all());
        return $this->successResponse(ProductResource::make($product),'Product created successfully', 201);
    }

    public function show($id)
    {
        try {
            $product = Product::findOrFail($id);
            
            return $this->successResponse(ProductResource::make($product),'Data retreved successfully', 201);
        } catch (\Exception $e) {
            return $this->notFoundResponse('Product not found.');
        }
    }

    public function update(UpdateProductRequest $request, $id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->update($request->all());

            return $this->successResponse(ProductResource::make($product),'Data updated successfully', 201);
        } catch (\Exception $e) {
            return $this->notFoundResponse('Product not found.');
        }
    }

    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();

            return $this->successResponse(null,'Product deleted successfully', 200);
        } catch (\Exception $e) {
            return $this->notFoundResponse('Product not found.');
        }
    }
}
