<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Proveedor;
use App\Models\MovimientoInventario;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\LengthAwarePaginator;

use App\Http\Requests\StoreProductoRequest;
use App\Http\Requests\UpdateProductoRequest;

class ProductoController extends Controller
{

    public function index(Request $request)
    {
        $query = Producto::with(['categoria','proveedor']);

        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function($qq) use ($q) {
                $qq->where('nombre', 'like', "%{$q}%")
                   ->orWhere('sku', 'like', "%{$q}%");
            });
        }
        if ($request->filled('categoria_id')) $query->where('categoria_id', $request->categoria_id);
        if ($request->filled('proveedor_id')) $query->where('proveedor_id', $request->proveedor_id);

        if ($request->get('export') === 'csv') {
            return $this->exportCsv(clone $query);
        }

        $productos   = $query->orderByDesc('id')->paginate(10)->withQueryString();
        $categorias  = Categoria::orderBy('nombre')->get(['id','nombre']);
        $proveedores = Proveedor::orderBy('nombre')->get(['id','nombre']);

        $hoy = now()->startOfDay();
        $lim = now()->addDays(30)->endOfDay();

        $bajoStock = Producto::whereColumn('stock', '<=', 'stock_minimo')
                    ->where('stock_minimo', '>', 0)->count();

        $porVencer = Producto::whereNotNull('fecha_caducidad')
                    ->whereBetween('fecha_caducidad', [$hoy, $lim])->count();

        return view('productos.index', compact('productos','categorias','proveedores','bajoStock','porVencer'));
    }

    protected function exportCsv($query)
    {
        $filename = 'productos_'.now()->format('Ymd_His').'.csv';
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($query) {
            $handle = fopen('php://output', 'w');
            
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'ID','Nombre','SKU','Precio','Unidad','Stock','Stock Min.',
                'Ubicación','Lote','Caducidad','Categoría','Proveedor'
            ]);

            $query->orderByDesc('id')->chunk(500, function($rows) use ($handle) {
                foreach ($rows as $p) {
                    fputcsv($handle, [
                        $p->id,
                        $p->nombre,
                        $p->sku,
                        number_format($p->precio, 2, '.', ''),
                        $p->unidad,
                        $p->stock,
                        $p->stock_minimo,
                        $p->ubicacion,
                        $p->lote,
                        optional($p->fecha_caducidad)->format('Y-m-d'),
                        optional($p->categoria)->nombre,
                        optional($p->proveedor)->nombre,
                    ]);
                }
            });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function create()
    {
        $categorias  = Categoria::orderBy('nombre')->pluck('nombre','id');
        $proveedores = Proveedor::orderBy('nombre')->pluck('nombre','id');

        return view('productos.create', compact('categorias','proveedores'));
    }


    public function store(Request $request)
    {
        $rules = [
            'nombre.*'        => ['required','string','max:255'],
            'sku.*'           => ['nullable','string','max:50'],
            'precio.*'        => ['required','numeric','min:0.01'],
            'unidad.*'        => ['required','string','max:20'],
            'stock.*'         => ['required','integer','min:0'],
            'stock_minimo.*'  => ['required','integer','min:0'],
            'ubicacion.*'     => ['nullable','string','max:255'],
            'lote.*'          => ['nullable','string','max:50'],
            'fecha_caducidad.*' => ['nullable','date'],
            'categoria_id.*'  => ['required','integer','exists:categorias,id'],
            'proveedor_id.*'  => ['required','integer','exists:proveedores,id'],
        ];

        $messages = [
            'nombre.*.required'       => 'Falta el nombre en una de las filas .',
            'precio.*.required'       => 'Falta el precio.',
            'precio.*.min'            => 'El precio debe ser > 0.',
            'stock.*.integer'         => 'El stock debe ser entero.',
            'categoria_id.*.required' => 'Selecciona la categoría en cada fila.',
            'proveedor_id.*.required' => 'Selecciona el proveedor en cada fila.',
        ];

        $data = $request->validate($rules, $messages);

        $filas = count($data['nombre'] ?? []);
        if ($filas === 0) {
            return back()->withErrors(['nombre.0' => 'Agrega al menos un producto.'])->withInput();
        }

        DB::transaction(function () use ($data, $filas) {
            for ($i = 0; $i < $filas; $i++) {
                Producto::create([
                    'nombre'        => $data['nombre'][$i],
                    'sku'           => $data['sku'][$i] ?? null,
                    'precio'        => $data['precio'][$i],
                    'unidad'        => $data['unidad'][$i],
                    'stock'         => $data['stock'][$i],
                    'stock_minimo'  => $data['stock_minimo'][$i],
                    'ubicacion'     => $data['ubicacion'][$i] ?? null,
                    'lote'          => $data['lote'][$i] ?? null,
                    'fecha_caducidad' => $data['fecha_caducidad'][$i] ?? null,
                    'categoria_id'  => $data['categoria_id'][$i],
                    'proveedor_id'  => $data['proveedor_id'][$i],
                ]);
            }
        });

        return redirect()->route('productos.index')->with('success', 'Productos creados con éxito. ');
    }

    public function edit(Producto $producto)
    {
        $categorias  = Categoria::orderBy('nombre')->pluck('nombre','id');
        $proveedores = Proveedor::orderBy('nombre')->pluck('nombre','id');
        return view('productos.edit', compact('producto','categorias','proveedores'));
    }

    public function update(UpdateProductoRequest $request, Producto $producto)
    {
        $producto->update($request->validated());
        return redirect()->route('productos.index')->with('success', 'Producto actualizado. ');
    }

    public function destroy(Producto $producto)
    {
        $producto->delete();
        return redirect()->route('productos.index')->with('success', 'Producto eliminado. ');
    }

    public function moverInventario(Request $request, Producto $producto)
    {
        $data = $request->validate([
            'tipo'     => ['required','in:ingreso,egreso,ajuste'],
            'cantidad' => ['required','integer','min:1'],
            'motivo'   => ['nullable','string','max:255'],
        ]);

        DB::transaction(function () use ($producto, $data) {
            
            if (Schema::hasTable('movimientos_inventario')) {
                MovimientoInventario::create([
                    'producto_id' => $producto->id,
                    'tipo'        => $data['tipo'],
                    'cantidad'    => $data['cantidad'],
                    'motivo'      => $data['motivo'] ?? null,
                    'usuario'     => 'admin', 
                ]);
            }


            if ($data['tipo'] === 'ingreso')  $producto->increment('stock', $data['cantidad']);
            if ($data['tipo'] === 'egreso')   $producto->decrement('stock', $data['cantidad']);
            if ($data['tipo'] === 'ajuste')   $producto->update(['stock' => $data['cantidad']]);
        });

        return back()->with('success', 'Movimiento registrado. ');
    }

    public function movimientos(Producto $producto, Request $request)
    {
        if (! Schema::hasTable('movimientos_inventario')) {
            $movs = new LengthAwarePaginator(
                collect([]), 0, 15, $request->get('page', 1),
                ['path' => $request->url(), 'query' => $request->query()]
            );

            return view('productos.movimientos', compact('producto','movs'))
                ->with('warning', 'Aún no hay histórico (y la tabla de movimientos no está creada).');
        }

        $movs = $producto->movimientos()->latest()->paginate(15);
        return view('productos.movimientos', compact('producto','movs'));
    }
}