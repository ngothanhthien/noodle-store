<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Meal>
 */
class MealFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'price' =>$this->faker->numberBetween(10000,200000),
            'name' =>$this->faker->name(),
            'image' =>$this->faker->text,
            'buy_amount'=>$this->faker->numberBetween(0,100),
            'description' =>$this->faker->text,
            'type' =>$this->faker->numberBetween(0,2),
        ];
    }
}
