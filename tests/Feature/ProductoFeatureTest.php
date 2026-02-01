<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Proveedor;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductoFeatureTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function puede_crear_producto_valido()
    {
        $categoria = Categoria::factory()->create();
        $proveedor = Proveedor::factory()->create();

        $data = [
            'nombre' => 'Mouse Gamer',
            'precio' => 25.50,
            'categoria_id' => $categoria->id,
            'proveedor_id' => $proveedor->id,
        ];

        $resp = $this->post(route('productos.store'), $data);

        $resp->assertRedirect(route('productos.index'));
        $this->assertDatabaseHas('productos', ['nombre' => 'Mouse Gamer']);
    }

    /** @test */
    public function valida_campos_obligatorios_y_reglas()
    {
        $resp = $this->post(route('productos.store'), []); // vacío
        $resp->assertSessionHasErrors(['nombre', 'precio', 'categoria_id', 'proveedor_id']);
    }

    /** @test */
    public function puede_actualizar_producto()
    {
        $producto = Producto::factory()->for(Categoria::factory())->for(Proveedor::factory())->create();

        $resp = $this->put(route('productos.update', $producto), [
            'nombre' => 'Teclado Mecánico',
            'precio' => 99.99,
            'categoria_id' => $producto->categoria_id,
            'proveedor_id' => $producto->proveedor_id,
        ]);

        $resp->assertRedirect(route('productos.index'));
        $this->assertDatabaseHas('productos', ['id' => $producto->id, 'nombre' => 'Teclado Mecánico']);
    }

    /** @test */
    public function eliminar_hace_soft_delete_y_se_puede_restaurar()
    {
        $producto = Producto::factory()->for(Categoria::factory())->for(Proveedor::factory())->create();

        $this->delete(route('productos.destroy', $producto))
             ->assertRedirect(route('productos.index'));

        $this->assertSoftDeleted('productos', ['id' => $producto->id]);

        // Restauramos
        $this->post(route('productos.restore', $producto->id))
             ->assertRedirect(route('productos.trash'));

        $this->assertDatabaseHas('productos', ['id' => $producto->id, 'deleted_at' => null]);
    }
}