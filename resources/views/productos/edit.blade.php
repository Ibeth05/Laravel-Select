@extends('layouts.app')
@section('title', 'Crear varios productos')

@section('content')
  <div class="card">
    <div class="card-body">
      <h2 class="h5 mb-3"><i class="bi bi-plus-circle"></i> Crear varios productos</h2>

      <form action="{{ route('productos.store') }}" method="POST">
        @csrf

        <div class="table-responsive">
          <table class="table table-sm align-middle" id="tabla-productos">
            <thead class="table-light">
              <tr>
                <th>Nombre</th>
                <th>SKU</th>
                <th style="width:120px;">Precio</th>
                <th style="width:120px;">Unidad</th>
                <th style="width:120px;">Stock</th>
                <th style="width:120px;">Mín.</th>
                <th>Categoría</th>
                <th>Proveedor</th>
                <th>Ubicación</th>
                <th>Lote</th>
                <th>Caducidad</th>
                <th style="width:60px;"></th>
              </tr>
            </thead>
            <tbody>
              <tr class="fila-item">
                <td><input name="nombre[]" class="form-control" required placeholder="Ej. Alcohol 70% 1L"></td>
                <td><input name="sku[]" class="form-control" placeholder="ALC-70-1L"></td>
                <td><input name="precio[]" type="number" step="0.01" min="0.01" class="form-control" required value="1.00"></td>
                <td>
                  <select name="unidad[]" class="form-select" required>
                    <option value="unidad">unidad</option>
                    <option value="litro">litro</option>
                    <option value="kg">kg</option>
                    <option value="caja">caja</option>
                  </select>
                </td>
                <td><input name="stock[]" type="number" min="0" class="form-control" required value="0"></td>
                <td><input name="stock_minimo[]" type="number" min="0" class="form-control" required value="0"></td>
                <td>
                  <select name="categoria_id[]" class="form-select" required>
                    <option value="">—</option>
                    @foreach ($categorias as $id => $nombre)
                      <option value="{{ $id }}">{{ $nombre }}</option>
                    @endforeach
                  </select>
                </td>
                <td>
                  <select name="proveedor_id[]" class="form-select" required>
                    <option value="">—</option>
                    @foreach ($proveedores as $id => $nombre)
                      <option value="{{ $id }}">{{ $nombre }}</option>
                    @endforeach
                  </select>
                </td>
                <td><input name="ubicacion[]" class="form-control" placeholder="Bodega A / Est. 3"></td>
                <td><input name="lote[]" class="form-control" placeholder="L-2026-01"></td>
                <td><input name="fecha_caducidad[]" type="date" class="form-control"></td>
                <td class="text-center">
                  <button type="button" class="btn btn-light text-danger btn-sm btn-eliminar" title="Quitar fila">
                    <i class="bi bi-x-lg"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="d-flex gap-2">
          <button type="button" id="btnAgregar" class="btn btn-outline-dark">
            <i class="bi bi-plus"></i> Agregar otra fila
          </button>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-check2-circle"></i> Guardar todo
          </button>
          <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-x-circle"></i> Cancelar
          </a>
        </div>
      </form>

      <div class="mt-3 small text-muted">
        Consejito: escribe la primera fila completa, duplica y ajusta lo mínimo. 😉
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const tabla = document.getElementById('tabla-productos').querySelector('tbody');
      const btnAgregar = document.getElementById('btnAgregar');

      btnAgregar.addEventListener('click', () => {
        const fila = tabla.querySelector('.fila-item');
        const clone = fila.cloneNode(true);

        clone.querySelectorAll('input').forEach(i => {
          if (i.name.includes('precio')) { i.value = '1.00'; return; }
          if (i.name.includes('stock'))  { i.value = '0'; return; }
          i.value = '';
        });
        clone.querySelectorAll('select').forEach(s => s.selectedIndex = 0);

        tabla.appendChild(clone);
      });

      tabla.addEventListener('click', (e) => {
        if (e.target.closest('.btn-eliminar')) {
          const filas = tabla.querySelectorAll('.fila-item');
          if (filas.length > 1) e.target.closest('tr').remove();
        }
      });
    });
  </script>
@endsection