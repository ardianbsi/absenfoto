<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'HRIS Absensi')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:300,400,500,600,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/css/tabler.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons@2.40.0/fonts/tabler-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        :root {
            --tblr-font-sans-serif: 'Figtree', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        body {
            font-family: var(--tblr-font-sans-serif);
        }
        .avatar-sm { width: 2rem; height: 2rem; }
        .avatar-md { width: 3rem; height: 3rem; }
        .avatar-lg { width: 5rem; height: 5rem; }
        .avatar-xl { width: 8rem; height: 8rem; }
    </style>
</head>
<body>
    <div class="page">
        @include('partials.sidebar')
        <header class="navbar navbar-expand-md d-none d-lg-flex d-print-none">
            <div class="container-fluid">
                <div class="navbar-nav flex-row order-md-last">
                    <div class="nav-item d-none d-md-flex me-3">
                        <div class="btn-list">
                            <button class="btn btn-ghost" id="theme-toggle">
                                <span class="nav-link-icon ti ti-moon"></span>
                            </button>
                        </div>
                    </div>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown">
                            <span class="avatar avatar-sm rounded-circle bg-primary text-white">
                                <span class="ti ti-user"></span>
                            </span>
                            <div class="d-none d-xl-block ps-2">
                                <div>{{ Auth::user()->name ?? 'User' }}</div>
                                <div class="small text-secondary">{{ Auth::user()->role ?? 'Staff' }}</div>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                            <a href="#" class="dropdown-item">
                                <span class="ti ti-user me-2"></span>Profile
                            </a>
                            <a href="#" class="dropdown-item">
                                <span class="ti ti-settings me-2"></span>Settings
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="#" class="dropdown-item text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <span class="ti ti-logout me-2"></span>Logout
                            </a>
                            <form id="logout-form" action="#" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <div class="page-wrapper">
            @if(session('success'))
                <div class="container-xl mt-3">
                    <div class="alert alert-success alert-dismissible" role="alert">
                        <div class="d-flex">
                            <div><span class="ti ti-check me-2"></span>{{ session('success') }}</div>
                        </div>
                        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                    </div>
                </div>
            @endif
            @if(session('error'))
                <div class="container-xl mt-3">
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <div class="d-flex">
                            <div><span class="ti ti-x me-2"></span>{{ session('error') }}</div>
                        </div>
                        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                    </div>
                </div>
            @endif
            @yield('content')
            <footer class="footer footer-transparent d-print-none">
                <div class="container-xl">
                    <div class="row text-center align-items-center flex-row-reverse">
                        <div class="col-12 col-lg-auto mt-1 mt-lg-0">
                            <ul class="list-inline list-inline-dots mb-0">
                                <li class="list-inline-item">
                                    HRIS Absensi v1.0
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/js/tabler.min.js"></script>
    <script>
        const themeToggle = document.getElementById('theme-toggle');
        const html = document.documentElement;
        const currentTheme = localStorage.getItem('theme') || 'light';
        if (currentTheme === 'dark') {
            html.setAttribute('data-bs-theme', 'dark');
            themeToggle.innerHTML = '<span class="nav-link-icon ti ti-sun"></span>';
        }
        themeToggle.addEventListener('click', function() {
            const isDark = html.getAttribute('data-bs-theme') === 'dark';
            if (isDark) {
                html.removeAttribute('data-bs-theme');
                localStorage.setItem('theme', 'light');
                themeToggle.innerHTML = '<span class="nav-link-icon ti ti-moon"></span>';
            } else {
                html.setAttribute('data-bs-theme', 'dark');
                localStorage.setItem('theme', 'dark');
                themeToggle.innerHTML = '<span class="nav-link-icon ti ti-sun"></span>';
            }
        });
        document.addEventListener('DOMContentLoaded', function() {
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            const tooltipList = [...tooltipTriggerList].map(el => new bootstrap.Tooltip(el));
            const deleteForms = document.querySelectorAll('.delete-form');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    if (confirm('Are you sure you want to delete this item?')) {
                        this.submit();
                    }
                });
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
