<header class="navbar navbar-expand-lg navbar-light d-print-none">
    <div class="container-xl">
        <button class="navbar-toggler sidebar-toggler d-lg-none me-2" type="button" data-bs-toggle="sidebar" aria-label="Toggle sidebar">
            <span class="navbar-toggler-icon"></span>
        </button>

        {{-- Search Bar --}}
        <form class="d-none d-sm-flex me-auto" action="#" method="get">
            <div class="input-icon">
                <span class="input-icon-addon">
                    <i class="ti ti-search"></i>
                </span>
                <input type="text" class="form-control" placeholder="Search..." aria-label="Search">
            </div>
        </form>

        <div class="navbar-nav flex-row order-md-last ms-auto gap-2">
            {{-- Dark Mode Toggle --}}
            @auth
            <div class="nav-item">
                <form action="{{ route('settings.theme') }}" method="POST" id="theme-toggle-form">
                    @csrf
                </form>
                <button type="button" class="nav-link px-2" onclick="document.getElementById('theme-toggle-form').submit();" title="Toggle theme">
                    <i class="ti ti-moon"></i>
                    <span class="d-none d-sm-inline ms-1">Dark Mode</span>
                </button>
            </div>
            @endauth

            {{-- Notifications Dropdown --}}
            @auth
            <div class="nav-item dropdown">
                <a href="#" class="nav-link position-relative" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ti ti-bell"></i>
                    <span class="badge badge-sm bg-red position-absolute top-0 end-0"></span>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-card">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Notifications</h3>
                        </div>
                        <div class="list-group list-group-flush list-group-hoverable">
                            <div class="list-group-item text-muted text-center py-4">
                                <i class="ti ti-bell-off"></i>
                                <p class="mb-0 mt-2">No new notifications</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endauth

            {{-- User Dropdown --}}
            @auth
            <div class="nav-item dropdown">
                <a href="#" class="nav-link d-flex lh-1 text-reset" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="d-none d-xl-block ps-2">
                        <div>{{ auth()->user()->name }}</div>
                        <div class="mt-1 small text-muted">{{ auth()->user()->role_name ?? 'User' }}</div>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <div class="dropdown-header text-center">
                        <div class="fw-bold">{{ auth()->user()->name }}</div>
                        <div class="small text-muted">{{ auth()->user()->email }}</div>
                    </div>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ route('settings.index') }}">
                        <i class="ti ti-settings dropdown-item-icon"></i> Settings
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="ti ti-logout dropdown-item-icon"></i> Logout
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
            @endauth

            @guest
            <div class="nav-item">
                <a href="{{ route('login') }}" class="nav-link">
                    <i class="ti ti-login"></i> Login
                </a>
            </div>
            @endguest
        </div>
    </div>
</header>
