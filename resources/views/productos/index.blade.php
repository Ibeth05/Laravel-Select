@extends('layouts.app')
@section('title', 'Listado de productos')

@section('content')
  {{-- Alertas --}}
  @if($bajoStock > 0 || $porVencer > 0)
    <div class="alert alert-warning d-flex justify-content-between align-items-center mt-3">
      <div>
        @if($bajoStock > 0)
          <span class="me-3"><i class="bi bi-exclamation-triangle-fill"></i> {{ $bajoStock }} con bajo stock</span>
        @endif
        @if($porVencer > 0)
          <span><i class="bi bi-calendar2-event-fill"></i> {{ $porVencer }} por caducar (≤ 30 días)</span>
        @endif
      </div>
      <a class="btn btn-sm btn-outline-dark" href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}">
        <i class="bi bi-filetype-csv"></i> Exportar CSV
      </a>
    </div>
  @endif

  <div class="d-flex flex-column flex-md-row gap-2 justify-content-between align-items-md-center my-3">
    <h1 class="h3 m-0">Listado de productos</h1>
    <div class="d-flex gap-2">
      <a class="btn btn-primary" href="{{ route('productos.create') }}">
        <i class="bi bi-plus-circle"></i> Nuevo (varios)
      </a>
    </div>
  </div>

  {{-- Filtros --}}
  <div class="card mb-3">
    <div class="card-body">
      <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-4">
          <label class="form-label">Buscar</label>
          <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Ej. Mouse, SKU...">
        </div>
        <div class="col-md-3">
          <label class="form-label">Categoría</label>
          <select name="categoria_id" class="form-select">
            <option value="">Todas</option>
            @foreach ($categorias as $c)
              <option value="{{ $c->id }}" @selected(request('categoria_id')==$c->id)>{{ $c->nombre }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Proveedor</label>
          <select name="proveedor_id" class="form-select">
            <option value="">Todos</option>
            @foreach ($proveedores as $p)
              <option value="{{ $p->id }}" @selected(request('proveedor_id')==$p->id)>{{ $p->nombre }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2 d-grid">
          <button class="btn btn-dark"><i class="bi bi-search"></i> Filtrar</button>
        </div>
      </form>
    </div>
  </div>

  {{-- Tabla --}}
  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Nombre / SKU</th>
              <th class="text-end">Precio</th>
              <th>Unidad</th>
              <th class="text-center">Stock</th>
              <th>Categoría</th>
              <th>Proveedor</th>
              <th style="width: 380px;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($productos as $producto)
              <tr>
                <td>{{ $producto->id }}</td>
                <td>
                  <div class="fw-semibold">{{ $producto->nombre }}</div>
                  <div class="text-muted small">{{ $producto->sku ?: '—' }}</div>
                  @if($producto->lote || $producto->fecha_caducidad)
                    <div class="small">
                      @if($producto->lote) <span class="badge text-bg-secondary">Lote: {{ $producto->lote }}</span> @endif
                      @if($producto->fecha_caducidad) <span class="badge text-bg-warning">Vence: {{ $producto->fecha_caducidad->format('d/m/Y') }}</span> @endif
                    </div>
                  @endif
                  @if($producto->ubicacion)
                    <div class="small text-muted">Ubicación: {{ $producto->ubicacion }}</div>
                  @endif
                </td>
                <td class="text-end">$ {{ number_format($producto->precio, 2, ',', '.') }}</td>
                <td>{{ $producto->unidad }}</td>
                <td class="text-center">
                  @if($producto->bajo_stock)
                    <span class="badge text-bg-danger">{{ $producto->stock }}</span><br>
                    <small class="text-danger">Mín: {{ $producto->stock_minimo }}</small>
                  @else
                    <span class="badge text-bg-success">{{ $producto->stock }}</span>
                  @endif
                </td>
                <td><span class="badge text-bg-secondary">{{ $producto->categoria?->nombre ?? '—' }}</span></td>
                <td><span class="badge text-bg-info">{{ $producto->proveedor?->nombre ?? '—' }}</span></td>
                <td>
                  <a class="btn btn-sm btn-outline-primary" href="{{ route('productos.edit', $producto) }}">
                    <i class="bi bi-pencil-square"></i> Editar
                  </a>

                  <a class="btn btn-sm btn-outline-dark" href="{{ route('productos.movimientos', $producto) }}">
                    <i class="bi bi-clock-history"></i> Movimientos
                  </a>

                  <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#movIn{{ $producto->id }}">
                    <i class="bi bi-box-arrow-in-down"></i> Ingreso
                  </button>

                  <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#movEg{{ $producto->id }}">
                    <i class="bi bi-box-arrow-up"></i> Egreso
                  </button>

                  <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#movAj{{ $producto->id }}">
                    <i class="bi bi-sliders"></i> Ajuste
                  </button>

                  <form action="{{ route('productos.destroy', $producto) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('¿Eliminar definitivamente este producto?');">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i> Eliminar</button>
                  </form>
                </td>
              </tr>
            @empty
              <tr><td colspan="8" class="text-center py-4">No hay productos aún 🙃</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="mt-3">
    {{ $productos->links() }}
  </div>

  {{-- ====== MODALES FUERA DE LA TABLA ====== --}}
  @foreach ($productos as $producto)

    {{-- Ingreso --}}
    <div class="modal fade" id="movIn{{ $producto->id }}" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <form action="{{ route('productos.movimiento', $producto) }}" method="POST" class="modal-content">
          @csrf
          <input type="hidden" name="tipo" value="ingreso">
          <div class="modal-header">
            <h5 class="modal-title">Ingreso — {{ $producto->nombre }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Cantidad</label>
              <input type="number" name="cantidad" min="1" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Motivo (opcional)</label>
              <input type="text" name="motivo" class="form-control" placeholder="Compra, devolución, etc.">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button class="btn btn-success"><i class="bi bi-check2-circle"></i> Guardar</button>
          </div>
        </form>
      </div>
    </div>

    {{-- Egreso --}}
    <div class="modal fade" id="movEg{{ $producto->id }}" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <form action="{{ route('productos.movimiento', $producto) }}" method="POST" class="modal-content">
          @csrf
          <input type="hidden" name="tipo" value="egreso">
          <div class="modal-header">
            <h5 class="modal-title">Egreso — {{ $producto->nombre }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Cantidad</label>
              <input type="number" name="cantidad" min="1" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Motivo (opcional)</label>
              <input type="text" name="motivo" class="form-control" placeholder="Venta, merma, etc.">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button class="btn btn-secondary"><i class="bi bi-check2-circle"></i> Guardar</button>
          </div>
        </form>
      </div>
    </div>

    {{-- Ajuste --}}
    <div class="modal fade" id="movAj{{ $producto->id }}" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <form action="{{ route('productos.movimiento', $producto) }}" method="POST" class="modal-content">
          @csrf
          <input type="hidden" name="tipo" value="ajuste">
          <div class="modal-header">
            <h5 class="modal-title">Ajuste — {{ $producto->nombre }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Nuevo stock</label>
              <input type="number" name="cantidad" min="1" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Motivo (opcional)</label>
              <input type="text" name="motivo" class="form-control" placeholder="Conteo físico, corrección, etc.">
            </div>
            <div class="alert alert-info small">
              El ajuste establece el <strong>stock</strong> exactamente a la cantidad ingresada.
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button class="btn btn-warning"><i class="bi bi-check2-circle"></i> Guardar</button>
          </div>
        </form>
      </div>
    </div>

  @endforeach
@endsection