<?php

namespace Database\Factories;

use App\Models\GiftCode;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class GiftCodeFactory extends Factory
{
    protected $model = GiftCode::class;

    public function definition(): array
    {
        return [
            'product_id' => $this->faker->numberBetween(1,11),
            'code' => $this->faker->unique()->bothify('????-????-????-????'),
            'used' => false,
        ];
    }
}
