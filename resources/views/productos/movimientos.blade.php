@extends('layouts.app')
@section('title', 'Movimientos — '.$producto->nombre)

@section('content')
  <div class="d-flex justify-content-between align-items-center my-3">
    <h1 class="h5 m-0"><i class="bi bi-clock-history"></i> Movimientos — {{ $producto->nombre }}</h1>
    <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary">← Volver</a>
  </div>

  @if(session('warning'))
    <div class="alert alert-warning">{{ session('warning') }}</div>
  @endif

  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Fecha</th>
              <th>Tipo</th>
              <th>Cantidad</th>
              <th>Motivo</th>
              <th>Usuario</th>
            </tr>
          </thead>
          <tbody>
            @forelse($movs as $m)
              <tr>
                <td>{{ optional($m->created_at)->format('d/m/Y H:i') ?? '—' }}</td>
                <td>
                  @if(($m->tipo ?? '') === 'ingreso')
                    <span class="badge text-bg-success">Ingreso</span>
                  @elseif(($m->tipo ?? '') === 'egreso')
                    <span class="badge text-bg-secondary">Egreso</span>
                  @elseif(($m->tipo ?? '') === 'ajuste')
                    <span class="badge text-bg-warning">Ajuste</span>
                  @else
                    —
                  @endif
                </td>
                <td>{{ $m->cantidad ?? '—' }}</td>
                <td>{{ $m->motivo ?? '—' }}</td>
                <td>{{ $m->usuario ?? '—' }}</td>
              </tr>
            @empty
              <tr><td colspan="5" class="text-center py-4">Sin movimientos</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="mt-3">
    {{ $movs->links() }}
  </div>
@endsection