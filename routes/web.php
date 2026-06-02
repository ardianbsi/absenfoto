<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\OvertimeController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ActivityLogController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Authenticated routes
Route::middleware('auth')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('index');
        Route::post('/theme', [SettingController::class, 'updateTheme'])->name('theme');
        Route::post('/profile', [SettingController::class, 'updateProfile'])->name('profile');
    });
    
    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('readAll');
        Route::get('/unread-count', [NotificationController::class, 'getUnreadCount'])->name('unreadCount');
    });
    
    // Employee routes
    Route::resource('employees', EmployeeController::class);
    
    // Attendance routes
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('index');
        Route::get('/check-in', [AttendanceController::class, 'checkIn'])->name('check-in');
        Route::post('/check-in', [AttendanceController::class, 'storeCheckIn'])->name('store-check-in');
        Route::get('/check-out/{id}', [AttendanceController::class, 'checkOut'])->name('check-out');
        Route::post('/check-out/{id}', [AttendanceController::class, 'storeCheckOut'])->name('store-check-out');
        Route::get('/my', [AttendanceController::class, 'myAttendance'])->name('my');
        Route::get('/{id}', [AttendanceController::class, 'show'])->name('show');
    });
    
    // Leave routes
    Route::prefix('leave')->name('leave.')->group(function () {
        Route::get('/', [LeaveController::class, 'index'])->name('index');
        Route::get('/create', [LeaveController::class, 'create'])->name('create');
        Route::post('/', [LeaveController::class, 'store'])->name('store');
        Route::get('/my', [LeaveController::class, 'myLeave'])->name('my');
        Route::get('/balance', [LeaveController::class, 'balance'])->name('balance');
        Route::get('/{id}', [LeaveController::class, 'show'])->name('show');
        Route::post('/{id}/approve', [LeaveController::class, 'approve'])->name('approve');
    });
    
    // Overtime routes
    Route::prefix('overtime')->name('overtime.')->group(function () {
        Route::get('/', [OvertimeController::class, 'index'])->name('index');
        Route::get('/create', [OvertimeController::class, 'create'])->name('create');
        Route::post('/', [OvertimeController::class, 'store'])->name('store');
        Route::get('/my', [OvertimeController::class, 'myOvertime'])->name('my');
        Route::get('/{id}', [OvertimeController::class, 'show'])->name('show');
        Route::post('/{id}/approve', [OvertimeController::class, 'approve'])->name('approve');
    });
    
    // Shift routes
    Route::prefix('shifts')->name('shifts.')->group(function () {
        Route::get('/', [ShiftController::class, 'index'])->name('index');
        Route::get('/create', [ShiftController::class, 'create'])->name('create');
        Route::post('/', [ShiftController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [ShiftController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ShiftController::class, 'update'])->name('update');
        Route::delete('/{id}', [ShiftController::class, 'destroy'])->name('destroy');
        Route::get('/assign/form', [ShiftController::class, 'assignForm'])->name('assign-form');
        Route::post('/assign', [ShiftController::class, 'assign'])->name('assign');
        Route::post('/assign-mass', [ShiftController::class, 'assignMass'])->name('assign-mass');
        Route::get('/schedule', [ShiftController::class, 'schedule'])->name('schedule');
        Route::get('/schedule-data', [ShiftController::class, 'getScheduleData'])->name('schedule-data');
    });
    
    // Department routes
    Route::resource('departments', DepartmentController::class);
    
    // Position routes
    Route::resource('positions', PositionController::class);
    
    // Holiday routes
    Route::resource('holidays', HolidayController::class);
    
    // Announcement routes
    Route::resource('announcements', AnnouncementController::class);
    
    // Report routes
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/daily', [ReportController::class, 'dailyReport'])->name('daily');
        Route::get('/weekly', [ReportController::class, 'weeklyReport'])->name('weekly');
        Route::get('/monthly', [ReportController::class, 'monthlyReport'])->name('monthly');
        Route::get('/late', [ReportController::class, 'lateReport'])->name('late');
        Route::get('/overtime', [ReportController::class, 'overtimeReport'])->name('overtime');
        Route::get('/leave', [ReportController::class, 'leaveReport'])->name('leave');
        Route::get('/employee/{id}', [ReportController::class, 'employeeReport'])->name('employee');
        Route::get('/export/csv', [ReportController::class, 'exportCSV'])->name('export-csv');
    });
    
    // Activity Log routes
    Route::prefix('activity-logs')->name('activity-logs.')->group(function () {
        Route::get('/', [ActivityLogController::class, 'index'])->name('index');
        Route::get('/{id}', [ActivityLogController::class, 'show'])->name('show');
    });
});
