<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Requests\Api\Product\ProductRequest;
use App\Http\Resources\Api\ProductResource;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Api\Product\UpdateProductRequest;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;
    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new \App\Http\Controllers\Api\ProductController();
    }

    public function test_store_creates_product_with_valid_data()
    {
        $request = new ProductRequest([
            'name' => 'Test Product',
            'description' => 'Test Description',
            'prices' => json_encode(['USD' => 100]),
            'stock_quantity' => 10,
        ]);
        $response = $this->controller->store($request);

        $responseData = json_decode($response->getContent());
        $this->assertEquals(201, $response->getStatusCode());

        $this->assertEquals('Product created successfully', $responseData->message);

        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'prices' => json_encode(['USD' => 100]),
            'stock_quantity' => 10,
        ]);
    }

    public function test_store_fails_with_invalid_data()
    {
        $request = new ProductRequest();
        $request->merge([
            'name' => '',
            'description' => 'Test Description',
            'prices' => ['USD' => 100],
            'stock_quantity' => 10,
        ]);
        $validator = Validator::make($request->all(), $request->rules());

        if ($validator->fails()) {
            $response = response()->json(['errors' => $validator->errors()], 422);
        } else {
            $response = $this->controller->store($request);
        }

        $responseData = json_decode($response->getContent());

        $this->assertEquals(422, $response->getStatusCode());
    }

    public function test_show_returns_product_when_found()
    {
        $product = Product::factory()->create();

        $response = $this->controller->show($product->id);
        $responseData = json_decode($response->getContent());

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals('Data retreved successfully', $responseData->message);
        $this->assertEquals($product->id, $responseData->data->id);
    }

    public function test_show_returns_not_found_when_product_does_not_exist()
    {
        $response = $this->controller->show(9999); 
        $responseData = json_decode($response->getContent());

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('Product not found.', $responseData->error);
    }

    public function test_update_returns_updated_product_when_found()
    {
        $product = Product::factory()->create();
        $request = new UpdateProductRequest([
            'name' => 'Updated Product',
            'description' => 'Updated Description',
            'prices' => json_encode(['USD' => 150]),
            'stock_quantity' => 20,
        ]);

        $response = $this->controller->update($request, $product->id);
        $responseData = json_decode($response->getContent());

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals('Data updated successfully', $responseData->message);
        $this->assertEquals('Updated Product', $responseData->data->name);
    }

    public function test_update_fails_with_invalid_data()
    {
        $product = Product::factory()->create();
        $request = new UpdateProductRequest([
            'name' => '', 
            'description' => '',
            'prices' => '', 
            'stock_quantity' => -1, 
        ]);

        $response = $this->controller->update($request, $product->id);
        $validator = Validator::make($request->all(), $request->rules());

        if ($validator->fails()) {
            $response = response()->json(['errors' => $validator->errors()], 422);
        } else {
            $response = $this->controller->store($request);
        }

        $responseData = json_decode($response->getContent());

        $this->assertEquals(422, $response->getStatusCode());
    }

    public function test_destroy_deletes_product_when_found()
    {
        $product = Product::factory()->create();

        $response = $this->controller->destroy($product->id);
        $responseData = json_decode($response->getContent());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Product deleted successfully', $responseData->message);
    }

    public function test_destroy_fails_when_product_does_not_exist()
    {
        $response = $this->controller->destroy(9999);
        $responseData = json_decode($response->getContent());

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('Product not found.', $responseData->error );
    }
}
