<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\User;
use App\Observers\AttendanceObserver;
use App\Observers\LeaveRequestObserver;
use App\Observers\UserObserver;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Attendance::observe(AttendanceObserver::class);
        LeaveRequest::observe(LeaveRequestObserver::class);
        User::observe(UserObserver::class);
    }
}
