<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckInRequest;
use App\Http\Requests\CheckOutRequest;
use App\Models\ActivityLog;
use App\Models\Attendance;
use App\Models\Employee;
use App\Services\AttendanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    protected $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function index(Request $request)
    {
        $query = Attendance::with('employee.user', 'employee.department', 'approver');

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        } else {
            $query->whereDate('date', now()->toDateString());
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('department_id')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        $attendances = $query->orderBy('created_at', 'desc')->paginate(20);
        $employees = Employee::active()->orderBy('full_name')->get();

        return view('attendances.index', compact('attendances', 'employees'));
    }

    public function checkIn()
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda tidak terdaftar sebagai karyawan.');
        }

        $todayAttendance = $this->attendanceService->getTodayAttendance($employee);

        return view('attendances.check-in', compact('employee', 'todayAttendance'));
    }

    public function storeCheckIn(CheckInRequest $request)
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return back()->with('error', 'Anda tidak terdaftar sebagai karyawan.');
        }

        try {
            $attendance = $this->attendanceService->checkIn($employee, $request->validated());

            ActivityLog::create([
                'user_id' => $user->id,
                'log_type' => 'check_in',
                'action' => 'check_in',
                'module' => 'attendance',
                'description' => 'Check in successful',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return redirect()->route('attendances.my')
                ->with('success', 'Check-in berhasil pada ' . $attendance->check_in->format('H:i:s') . '.');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function checkOut($id)
    {
        $attendance = Attendance::with('employee.user')->findOrFail($id);

        $this->authorize('update', $attendance);

        return view('attendances.check-out', compact('attendance'));
    }

    public function storeCheckOut(CheckOutRequest $request, $id)
    {
        $attendance = Attendance::with('employee.user')->findOrFail($id);

        $this->authorize('update', $attendance);

        $employee = $attendance->employee;

        try {
            $attendance = $this->attendanceService->checkOut($employee, $request->validated());

            ActivityLog::create([
                'user_id' => Auth::id(),
                'log_type' => 'check_out',
                'action' => 'check_out',
                'module' => 'attendance',
                'description' => 'Check out successful',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return redirect()->route('attendances.my')
                ->with('success', 'Check-out berhasil pada ' . $attendance->check_out->format('H:i:s') . '.');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function show($id)
    {
        $attendance = Attendance::with(['employee.user', 'employee.department', 'employee.position', 'approver', 'logs'])
            ->findOrFail($id);

        return view('attendances.show', compact('attendance'));
    }

    public function myAttendance(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda tidak terdaftar sebagai karyawan.');
        }

        $query = Attendance::where('employee_id', $employee->id);

        if ($request->filled('month')) {
            $query->whereMonth('date', $request->month);
        } else {
            $query->whereMonth('date', now()->month);
        }

        if ($request->filled('year')) {
            $query->whereYear('date', $request->year);
        } else {
            $query->whereYear('date', now()->year);
        }

        $attendances = $query->orderBy('date', 'desc')->paginate(20);
        $todayAttendance = $this->attendanceService->getTodayAttendance($employee);

        return view('attendances.my', compact('attendances', 'employee', 'todayAttendance'));
    }
}
