<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MaterialFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "name"=>$this->faker->name(),
            'unit'=>$this->faker->randomLetter(),
            'supply'=>$this->faker->numberBetween(0,100),
        ];
    }
}
