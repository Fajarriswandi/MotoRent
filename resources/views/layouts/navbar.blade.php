<nav class="navbar fixed-top navbar-expand-lg navbar-light bg-light bg-white bgPrimaryCostume123">

    <div class="container-fluid containerApp123 pt-1 pb-1">

        <!-- Logo -->
        <a class="navbar-brand" href="/"><img src="{{ asset('images/logo.png') }}" alt="Logo MotoRent" width="150">
        </a>

        <!-- Button Nav Mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse navbarCostume" id="navbarNav">

            <!-- Main Menu -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 mainMenu">
                @guest
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                @else

                        @if (Auth::user()->role === 'admin' || Auth::user()->role === 'manager')
                            @if (canAccess('dashboard'))
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->is('admin/dashboard') ? 'active' : '' }}"
                                        href="{{ route('admin.dashboard') }}">
                                        <x-icon name="si:dashboard-fill" class="sm me-1"/>Dashboard
                                    </a>
                                </li>
                            @endif

                            @if (canAccess('motorbikes', 'read'))
                                <li class="nav-item"><a class="nav-link {{ request()->is('admin/motorbikes*') ? 'active' : '' }}"
                                        href="{{ route('motorbikes.index') }}"><x-icon name="fluent:vehicle-motorcycle-24-filled" class="sm me-1"/>Motorbikes</a></li>
                            @endif

                            @if (canAccess('rentals', 'read'))
                                <li class="nav-item"><a class="nav-link {{ request()->is('admin/rentals*') ? 'active' : '' }}"
                                        href="{{ route('admin.rentals.index') }}"><x-icon name="heroicons-solid:key" class="sm me-1"/>Rentals</a></li>
                            @endif

                            @if (canAccess('customers', 'read'))
                                <li class="nav-item dropdown ">
                                    <a class="nav-link dropdown-toggle {{ request()->is('admin/customers*') ? 'active' : '' }}" href="#"
                                        id="navbarCustomerDropdown" role="button" data-bs-toggle="dropdown">
                                        <x-icon name="mage:users-fill" class="sm me-1"/>Customers
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        @if (canAccess('customers', 'read'))
                                            <li><a class="dropdown-item" href="{{ route('admin.customers.index') }}">Daftar Customer</a>
                                            </li>
                                        @endif
                                        @if (canAccess('customers', 'create'))
                                            <li><a class="dropdown-item" href="{{ route('admin.customers.create') }}">Tambah Customer</a>
                                            </li>
                                        @endif
                                    </ul>
                                </li>
                            @endif


                            @if (canAccess('users', 'read'))
                                <li class="nav-item"><a
                                        class="nav-link {{ request()->is('admin/users*') || request()->is('users*') ? 'active' : '' }}"
                                        href="{{ route('users.index') }}"><x-icon name="game-icons:padlock-open" class="sm me-1"/>Users</a></li>
                            @endif

                            @if (canAccess('reports', 'read'))
                                <li class="nav-item"><a class="nav-link {{ request()->is('admin/reports*') ? 'active' : '' }}"
                                        href="{{ route('reports.index') }}"><x-icon name="majesticons:analytics-plus" class="sm me-1"/> Reports</a>
                                </li>
                            @endif

                            <!-- Bila login dengan role customer, tapi belum butuh -->
                            @if(Auth::user()->role === 'customer')
                                <li class="nav-item"><a class="nav-link" href="/customer/dashboard">Dashboard</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('rentals.index') }}">Riwayat Sewa</a></li>
                            @endif

                        @endif

                    </ul>

                    <!-- Menu Right -->
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown"
                                role="button" data-bs-toggle="dropdown">
                                @if(Auth::user()->profile_photo)
                                    <img src="{{ asset('storage/' . Auth::user()->profile_photo) }}" class="rounded-circle me-2"
                                        width="30" height="30" alt="Profile">
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
                    </ul>


                @endguest
        </div>

    </div>
</nav>