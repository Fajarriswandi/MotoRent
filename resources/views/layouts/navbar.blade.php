<!-- Offcanvas Navigation for Mobile -->
<nav class="navbar fixed-top navbar-expand-lg navbar-light bg-white shadow-sm bgPrimaryCostume123">
    <div class="container-fluid containerApp123 pt-1 pb-1">
        <!-- Logo -->
        <a class="navbar-brand me-3" href="/">
            <img src="{{ asset('images/logo.png') }}" alt="Logo MotoRent" width="130">
        </a>

        <!-- Toggle Button Mobile -->
        <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNav" aria-controls="offcanvasNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Main Menu Horizontal (Desktop Only) -->
        <div class="collapse navbar-collapse navbarCostume d-none d-lg-flex w-100 justify-content-between align-items-center" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 mainMenu">
                @if (canAccess('dashboard'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                            <x-icon name="si:dashboard-fill" class="sm me-1"/>Dashboard
                        </a>
                    </li>
                @endif
                @if (canAccess('motorbikes', 'read'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/motorbikes*') ? 'active' : '' }}" href="{{ route('motorbikes.index') }}">
                            <x-icon name="fluent:vehicle-motorcycle-24-filled" class="sm me-1"/>Motorbikes
                        </a>
                    </li>
                @endif
                @if (canAccess('rentals', 'read'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/rentals*') ? 'active' : '' }}" href="{{ route('admin.rentals.index') }}">
                            <x-icon name="heroicons-solid:key" class="sm me-1"/>Rentals
                        </a>
                    </li>
                @endif
                @if (canAccess('customers', 'read'))
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->is('admin/customers*') ? 'active' : '' }}" href="#" id="navbarCustomerDropdown" role="button" data-bs-toggle="dropdown">
                            <x-icon name="mage:users-fill" class="sm me-1"/>Customers
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('admin.customers.index') }}">Daftar Customer</a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.customers.create') }}">Tambah Customer</a></li>
                        </ul>
                    </li>
                @endif
                @if (canAccess('users', 'read'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/users*') || request()->is('users*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                            <x-icon name="game-icons:padlock-open" class="sm me-1"/>Users
                        </a>
                    </li>
                @endif
                @if (canAccess('reports', 'read'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/reports*') ? 'active' : '' }}" href="{{ route('reports.index') }}">
                            <x-icon name="majesticons:analytics-plus" class="sm me-1"/>Reports
                        </a>
                    </li>
                @endif
            </ul>

            <!-- Right Profile -->
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        @if(Auth::user()->profile_photo)
                            <img src="{{ asset('storage/' . Auth::user()->profile_photo) }}" class="rounded-circle me-2" width="30" height="30" alt="Profile">
                        @endif
                        {{ Auth::user()->name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('profile') }}">Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item">Logout</button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Offcanvas Sidebar for Mobile -->
<div class="offcanvas offcanvas-start d-lg-none  " tabindex="-1" id="offcanvasNav" aria-labelledby="offcanvasNavLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasNavLabel">Menu</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body navbarCostume">
        <ul class="navbar-nav mainMenu">
            @if (canAccess('dashboard'))
                <li class="nav-item"><a class="nav-link {{ request()->is('admin/dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            @endif
            @if (canAccess('motorbikes', 'read'))
                <li class="nav-item"><a class="nav-link {{ request()->is('admin/motorbikes*') ? 'active' : '' }}" href="{{ route('motorbikes.index') }}">Motorbikes</a></li>
            @endif
            @if (canAccess('rentals', 'read'))
                <li class="nav-item"><a class="nav-link {{ request()->is('admin/rentals*') ? 'active' : '' }}" href="{{ route('admin.rentals.index') }}">Rentals</a></li>
            @endif
            @if (canAccess('customers', 'read'))
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->is('admin/customers*') ? 'active' : '' }}" href="#" data-bs-toggle="dropdown">Customers</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('admin.customers.index') }}">Daftar Customer</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.customers.create') }}">Tambah Customer</a></li>
                    </ul>
                </li>
            @endif
            @if (canAccess('users', 'read'))
                <li class="nav-item"><a class="nav-link {{ request()->is('admin/users*') || request()->is('users*') ? 'active' : '' }}" href="{{ route('users.index') }}">Users</a></li>
            @endif
            @if (canAccess('reports', 'read'))
                <li class="nav-item"><a class="nav-link {{ request()->is('admin/reports*') ? 'active' : '' }}" href="{{ route('reports.index') }}">Reports</a></li>
            @endif
            <li><hr class="dropdown-divider"></li>
            <li class="nav-item mt-3"><a class="nav-link" href="{{ route('profile') }}">Profile</a></li>
            <li class="nav-item mt-3">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger w-100">Logout</button>
                </form>
            </li>
        </ul>
    </div>
</div>
