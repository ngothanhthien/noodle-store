<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MealFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'price' =>$this->faker->numberBetween(10000,200000),
            'name' =>$this->faker->name(),
        ];
    }
}
