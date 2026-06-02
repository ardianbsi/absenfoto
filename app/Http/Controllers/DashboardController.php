<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index()
    {
        $user = Auth::user();
        $roleName = $user->role ? $user->role->name : 'employee';

        $data = match($roleName) {
            'super_admin' => $this->dashboardService->getSuperAdminDashboard(),
            'hr' => $this->dashboardService->getHrDashboard(),
            'manager' => $this->dashboardService->getManagerDashboard($user->id),
            default => $this->dashboardService->getEmployeeDashboard($user->id),
        };

        return view('dashboard.index', $data);
    }
}
