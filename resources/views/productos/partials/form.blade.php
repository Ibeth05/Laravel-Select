{{-- Campos compartidos para crear/editar. Humano: mensajes claros y placeholders útiles. --}}
<div class="row g-3">

  <div class="col-12">
    <label for="nombre" class="form-label">Nombre del producto</label>
    <input type="text" id="nombre" name="nombre"
           class="form-control @error('nombre') is-invalid @enderror"
           value="{{ old('nombre', $producto->nombre ?? '') }}"
           placeholder="Ej. Laptop HP 15" required>
    @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

  <div class="col-md-4">
    <label for="precio" class="form-label">Precio</label>
    <div class="input-group">
      <span class="input-group-text">$</span>
      <input type="number" id="precio" name="precio" step="0.01" min="0.01"
             class="form-control @error('precio') is-invalid @enderror"
             value="{{ old('precio', $producto->precio ?? '') }}"
             placeholder="0.00" required>
      @error('precio') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
  </div>

  <div class="col-md-4">
    <label for="categoria_id" class="form-label">Categoría</label>
    <select id="categoria_id" name="categoria_id"
            class="form-select @error('categoria_id') is-invalid @enderror" required>
      <option value="">-- Selecciona una categoría --</option>
      @foreach ($categorias as $id => $nombre)
        <option value="{{ $id }}" @selected(old('categoria_id', $producto->categoria_id ?? null) == $id)>
          {{ $nombre }}
        </option>
      @endforeach
    </select>
    @error('categoria_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

  <div class="col-md-4">
    <label for="proveedor_id" class="form-label">Proveedor</label>
    <select id="proveedor_id" name="proveedor_id"
            class="form-select @error('proveedor_id') is-invalid @enderror" required>
      <option value="">-- Selecciona un proveedor --</option>
      @foreach ($proveedores as $id => $nombre)
        <option value="{{ $id }}" @selected(old('proveedor_id', $producto->proveedor_id ?? null) == $id)>
          {{ $nombre }}
        </option>
      @endforeach
    </select>
    @error('proveedor_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

</div>