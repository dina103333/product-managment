<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Product;
use App\Models\User;

class ProductFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_destroy_product_when_not_found()
    {
        $user = User::factory()->create(); 
        $response = $this->actingAs($user, 'api')->deleteJson('/api/products/9999'); // Non-existent product ID
    
        $response->assertStatus(404)
                 ->assertJson([
                     'error' => 'Product not found.',
                 ]);
    }

    public function test_destroy_product_when_found()
    {
        $user = User::factory()->create(); 
        $product = Product::factory()->create();

        $response = $this->actingAs($user, 'api')->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Product deleted successfully',
                ]);
    }

    public function test_update_product_with_invalid_data()
    {
        $user = User::factory()->create(); 
        $product = Product::factory()->create();

        $response = $this->actingAs($user, 'api')->putJson("/api/products/{$product->id}", [
            'name' => '', 
            'description' => '',
            'prices' => '',
            'stock_quantity' => -1, 
        ]);

        $response->assertStatus(422)
                ->assertJson([
                    'errors'=> [
                        'message'=> [
                            "name"=> [
                                "The name field is required."
                            ],
                            "description" => [
                                "The description field is required."
                            ],
                            "prices" => [
                                "The prices field is required."
                            ],
                            "stock_quantity" => [
                                "The stock quantity field must be at least 0."
                            ]
                        ]
                    ]
                ]);
    }


    public function test_update_product_with_valid_data()
    {
        $user = User::factory()->create(); 
        $product = Product::factory()->create();

        $response = $this->actingAs($user, 'api')->putJson("/api/products/{$product->id}", [
            'name' => 'Updated Product',
            'description' => 'Updated Description',
            'prices' => ['USD' => 150],
            'stock_quantity' => 20,
        ]);

        $response->assertStatus(201)
                ->assertJson([
                        'message' => 'Data updated successfully',
                        'data' => [
                            'name' => 'Updated Product',
                        ],
                    ]);
    }

    public function test_show_product_when_not_found()
    {
        $user = User::factory()->create(); 
        $response = $this->actingAs($user, 'api')->getJson('/api/products/9999'); // Non-existent product ID

        $response->assertStatus(404)
                ->assertJson([
                    'error' => 'Product not found.',
                ]);
    }

    public function test_show_product_when_found()
    {
        $user = User::factory()->create(); 
        $product = Product::factory()->create();

        $response = $this->actingAs($user, 'api')->getJson("/api/products/{$product->id}");

        $response->assertStatus(201)
                ->assertJson([
                    'message' => 'Data retreved successfully',
                    'data' => [
                        'id' => $product->id,
                    ],
                ]);
    }

    public function test_store_product_with_invalid_data()
    {
        $user = User::factory()->create(); 
        $response = $this->actingAs($user, 'api')->postJson('/api/products', [
            'name' => '', 
            'description' => '',
            'prices' => '', 
            'stock_quantity' => -1, 
        ]);

        $response->assertStatus(422)
                ->assertJson([   
                    'errors'=> [                 
                        'message' => [
                            'name' => [
                                'The name field is required.',
                            ],
                            'description' => [
                                'The description field is required.',
                            ],
                            'prices' => [
                                'The prices field is required.'
                            ],
                            'stock_quantity' => [
                                'The stock quantity field must be at least 0.'
                            ]
                        ]
                    ]
                ]);
    }

    public function test_store_product_with_valid_data()
    {
        $user = User::factory()->create(); 
        $response = $this->actingAs($user, 'api')->postJson('/api/products', [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'prices' => json_encode(['USD' => 100]),
            'stock_quantity' => 10,
        ]);

        $response->assertStatus(422)
                ->assertJson([
                    'errors' => [
                        'message' => [
                            'prices' => [
                                'The prices field must be an array.',
                            ],
                        ]
                    ]
                ]);
    }
}
