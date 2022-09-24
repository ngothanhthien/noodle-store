<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'state' =>$this->faker->numberBetween(0,3),
            'total_price' =>$this->faker->numberBetween(100000,100000),
            'payment_gate' =>$this->faker->numberBetween(0,2),
        ];
    }
}
