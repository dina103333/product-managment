<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'prices' => json_encode(['USD' => $this->faker->numberBetween(10, 1000)]),
            'stock_quantity' => $this->faker->numberBetween(1, 100),
        ];
    }
}
