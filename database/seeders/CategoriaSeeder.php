<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categoria;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $nombres = [
            'Electrónica','Hogar','Ropa','Juguetes',
            'Alimentos','Bebidas','Aseo y Limpieza',
            'Medicina','Farmacia','Papelería','Perfumería',
        ];

        foreach ($nombres as $nombre) {
            Categoria::firstOrCreate(['nombre' => $nombre]);
        }
    }
}