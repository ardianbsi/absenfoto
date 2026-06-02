<aside class="navbar navbar-vertical navbar-expand-lg" data-bs-theme="dark">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu" aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <h1 class="navbar-brand navbar-brand-autodark">
            <a href="{{ route('dashboard') }}">
                <img src="{{ asset('logo.svg') }}" width="110" height="32" alt="HRIS" class="navbar-brand-image">
            </a>
        </h1>
        <div class="collapse navbar-collapse" id="sidebar-menu">
            <ul class="navbar-nav pt-lg-3">
                @auth
                    @php
                        $role = auth()->user()->role?->name;
                        $isSuperAdmin = auth()->user()->is_super_admin;
                        $isHr = auth()->user()->is_hr;
                        $isManager = auth()->user()->is_manager;
                        $isEmployee = auth()->user()->is_employee;
                    @endphp

                    {{-- SUPER ADMIN & HR MENU --}}
                    @if($isSuperAdmin || $isHr)
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-dashboard"></i></span>
                                <span class="nav-link-title">Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('employees.*') ? 'active' : '' }}" href="{{ route('employees.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-users"></i></span>
                                <span class="nav-link-title">Employees</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('departments.*') ? 'active' : '' }}" href="{{ route('departments.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-building"></i></span>
                                <span class="nav-link-title">Departments</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('positions.*') ? 'active' : '' }}" href="{{ route('positions.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-briefcase"></i></span>
                                <span class="nav-link-title">Positions</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('shifts.*') ? 'active' : '' }}" href="{{ route('shifts.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-calendar-time"></i></span>
                                <span class="nav-link-title">Shifts & Schedule</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('attendance.*') ? 'active' : '' }}" href="{{ route('attendance.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-fingerprint"></i></span>
                                <span class="nav-link-title">Attendance</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('leave.*') ? 'active' : '' }}" href="{{ route('leave.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-calendar-check"></i></span>
                                <span class="nav-link-title">Leave Management</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('overtime.*') ? 'active' : '' }}" href="{{ route('overtime.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-clock-plus"></i></span>
                                <span class="nav-link-title">Overtime</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-report"></i></span>
                                <span class="nav-link-title">Reports</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('holidays.*') ? 'active' : '' }}" href="{{ route('holidays.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-gift"></i></span>
                                <span class="nav-link-title">Holidays</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('announcements.*') ? 'active' : '' }}" href="{{ route('announcements.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-bell"></i></span>
                                <span class="nav-link-title">Announcements</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('activity-logs.*') ? 'active' : '' }}" href="{{ route('activity-logs.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-list"></i></span>
                                <span class="nav-link-title">Activity Logs</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-settings"></i></span>
                                <span class="nav-link-title">Settings</span>
                            </a>
                        </li>

                    {{-- MANAGER MENU --}}
                    @elseif($isManager)
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-dashboard"></i></span>
                                <span class="nav-link-title">Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('attendance.*') ? 'active' : '' }}" href="{{ route('attendance.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-fingerprint"></i></span>
                                <span class="nav-link-title">Attendance</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('leave.*') ? 'active' : '' }}" href="{{ route('leave.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-calendar-check"></i></span>
                                <span class="nav-link-title">Leave</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('overtime.*') ? 'active' : '' }}" href="{{ route('overtime.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-clock-plus"></i></span>
                                <span class="nav-link-title">Overtime</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-report"></i></span>
                                <span class="nav-link-title">Reports</span>
                            </a>
                        </li>

                    {{-- EMPLOYEE MENU --}}
                    @elseif($isEmployee)
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-dashboard"></i></span>
                                <span class="nav-link-title">Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('attendance.*') ? 'active' : '' }}" href="{{ route('attendance.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-fingerprint"></i></span>
                                <span class="nav-link-title">My Attendance</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('leave.create') ? 'active' : '' }}" href="{{ route('leave.create') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-plus"></i></span>
                                <span class="nav-link-title">Submit Leave</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('leave.index') ? 'active' : '' }}" href="{{ route('leave.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-calendar-check"></i></span>
                                <span class="nav-link-title">My Leave</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('overtime.create') ? 'active' : '' }}" href="{{ route('overtime.create') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-plus"></i></span>
                                <span class="nav-link-title">Submit Overtime</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('overtime.index') ? 'active' : '' }}" href="{{ route('overtime.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-clock-plus"></i></span>
                                <span class="nav-link-title">My Overtime</span>
                            </a>
                        </li>
                    @endif
                @endauth
            </ul>
        </div>
    </div>
</aside>
