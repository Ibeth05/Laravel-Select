<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Proveedor;

class ProveedorSeeder extends Seeder
{
    public function run(): void
    {
        $proveedores = [
            ['nombre' => 'PRONACA',                 'email' => 'contacto@pronaca.com',             'telefono' => '0990000001'],
            ['nombre' => 'Moderna Alimentos',       'email' => 'info@moderna.com.ec',              'telefono' => '0990000002'],
            ['nombre' => 'La Fabril',               'email' => 'contacto@lafabril.com.ec',         'telefono' => '0990000003'],
            ['nombre' => 'Tesalia cbc',             'email' => 'contacto@tesaliacbc.com',          'telefono' => '0990000004'],
            ['nombre' => 'Arca Continental',        'email' => 'info@arcacontal.com',              'telefono' => '0990000005'],
            ['nombre' => 'Cervecería Nacional',     'email' => 'contacto@cervecerianacional.ec',   'telefono' => '0990000006'],
            ['nombre' => 'Grupo DIFARE',            'email' => 'info@difare.com.ec',               'telefono' => '0990000007'],
            ['nombre' => 'Acromax',                 'email' => 'contacto@acromax.com.ec',          'telefono' => '0990000008'],
            ['nombre' => 'Yanbal Ecuador',          'email' => 'servicio@yanbal.com',              'telefono' => '0990000009'],
            ['nombre' => 'Belcorp (ésika/L’Bel/Cyzone)', 'email' => 'contacto@belcorp.biz',       'telefono' => '0990000010'],
            ['nombre' => 'Papelera Nacional (PANASA)', 'email' => 'comercial@panasa.com.ec',       'telefono' => '0990000011'],
            ['nombre' => 'Quifatex',                'email' => 'info@quifatex.com',                'telefono' => '0990000012'],
        ];

        foreach ($proveedores as $p) {
            Proveedor::firstOrCreate(['nombre' => $p['nombre']], $p);
        }
    }
}