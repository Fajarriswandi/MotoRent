<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>MotoRent</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  @vite(['resources/scss/app.scss', 'resources/js/app.js'])

</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
      <a class="navbar-brand" href="/">MotoRent</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          @guest
        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
      @else
      @if (Auth::user()->role === 'admin' || Auth::user()->role === 'manager')
      @if (canAccess('dashboard'))
      <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    @endif

      @if (canAccess('motorbikes', 'read'))
      <li class="nav-item"><a class="nav-link {{ request()->is('admin/motorbikes*') ? 'active' : '' }}"
      href="{{ route('motorbikes.index') }}">Motorbikes</a></li>
    @endif

      @if (canAccess('rentals', 'read'))
      <li class="nav-item"><a class="nav-link {{ request()->is('admin/rentals*') ? 'active' : '' }}"
      href="{{ route('admin.rentals.index') }}">Rentals</a></li>
    @endif

      @if (canAccess('customers', 'read'))
      <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle" href="#" id="navbarCustomerDropdown" role="button"
      data-bs-toggle="dropdown">
      Customers
      </a>
      <ul class="dropdown-menu dropdown-menu-end">
      @if (canAccess('customers', 'read'))
      <li><a class="dropdown-item" href="{{ route('admin.customers.index') }}">Daftar Customer</a></li>
    @endif
      @if (canAccess('customers', 'create'))
      <li><a class="dropdown-item" href="{{ route('admin.customers.create') }}">Tambah Customer</a></li>
    @endif
      </ul>
      </li>
    @endif

      @if (canAccess('users', 'read'))
      <li class="nav-item"><a
      class="nav-link {{ request()->is('admin/users*') || request()->is('users*') ? 'active' : '' }}"
      href="{{ route('users.index') }}"><i class="bi bi-people"></i> Users</a></li>
    @endif

      @if (canAccess('reports', 'read'))
      <li class="nav-item"><a class="nav-link {{ request()->is('admin/reports*') ? 'active' : '' }}"
      href="{{ route('reports.index') }}"><i class="bi bi-file-earmark-bar-graph"></i> Reports</a></li>
    @endif
    @elseif(Auth::user()->role === 'customer')
      <li class="nav-item"><a class="nav-link" href="/customer/dashboard">Dashboard</a></li>
      <li class="nav-item"><a class="nav-link" href="{{ route('rentals.index') }}">Riwayat Sewa</a></li>
    @endif

      {{-- Profile Dropdown --}}
      <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button"
        data-bs-toggle="dropdown">
        @if(Auth::user()->profile_photo)
      <img src="{{ asset('storage/' . Auth::user()->profile_photo) }}" class="rounded-circle me-2" width="30"
      height="30" alt="Profile">
    @endif
        {{ Auth::user()->name }}
      </a>
      <ul class="dropdown-menu dropdown-menu-end">
        <li><a class="dropdown-item" href="{{ route('profile') }}">Profile</a></li>
        <li>
        <hr class="dropdown-divider">
        </li>
        <li>
        <form action="{{ route('logout') }}" method="POST">
          @csrf
          <button type="submit" class="dropdown-item">Logout</button>
        </form>
        </li>
      </ul>
      </li>
    @endguest
        </ul>
      </div>
    </div>
  </nav>

  <main class="container mt-4">
    @yield('content')
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  @stack('scripts')
</body>

</html>