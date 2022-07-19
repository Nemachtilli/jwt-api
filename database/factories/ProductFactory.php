<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "name" => $this->faker->realText(25),
            "description" => $this->faker->text(350),
            "price" => $this->faker->randomFloat(2, 5, 50),
            "available" => $this->faker->boolean,
        ];
    }
}
