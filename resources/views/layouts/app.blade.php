<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>@yield('title', 'Productos')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  {{-- Bootstrap 5 + Icons --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

  <style>
    body { min-height:100vh; display:flex; flex-direction:column; background:#f7f8fa; }
    main { flex:1; padding-top:1rem; padding-bottom:2rem; }
    .card { border:0; box-shadow: 0 6px 18px rgba(0,0,0,.06); }
    .table td, .table th { vertical-align: middle; }
  </style>
</head>
<body>

  {{-- Navbar --}}
  <nav class="navbar navbar-expand-lg bg-dark navbar-dark">
    <div class="container">
      <a class="navbar-brand" href="{{ route('productos.index') }}">
        <i class="bi bi-box-seam"></i> Mi Inventario
      </a>
      <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div id="nav" class="collapse navbar-collapse">
        <ul class="navbar-nav me-auto">
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('productos.index') ? 'active' : '' }}"
               href="{{ route('productos.index') }}"><i class="bi bi-list-ul"></i> Productos</a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('productos.create') ? 'active' : '' }}"
               href="{{ route('productos.create') }}"><i class="bi bi-plus-circle"></i> Nuevo (varios)</a>
          </li>
        </ul>
        <span class="navbar-text">Laravel ♥ Bootstrap</span>
      </div>
    </div>
  </nav>

  <main class="container">
    @if (session('success'))
      <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
      </div>
    @endif

    @yield('content')
  </main>

  <footer class="bg-light border-top">
    <div class="container py-3 text-center text-muted">
      Hecho con <span style="color:#e25555;">♥</span> en Guayaquil — {{ date('Y') }}
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>