<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProveedorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre'   => $this->faker->unique()->company(),
            'email'    => $this->faker->unique()->safeEmail(),
            'telefono' => $this->faker->numerify('0#########'),
        ];
    }
}