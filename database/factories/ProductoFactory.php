<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Categoria;
use App\Models\Proveedor;

class ProductoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre'       => ucfirst($this->faker->words(2, true)),
            'precio'       => $this->faker->randomFloat(2, 1, 999),
            'categoria_id' => Categoria::factory(),
            'proveedor_id' => Proveedor::factory(),
        ];
    }
}