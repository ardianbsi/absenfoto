<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApproveOvertimeRequest;
use App\Http\Requests\StoreOvertimeRequest;
use App\Models\ActivityLog;
use App\Models\Employee;
use App\Models\OvertimeRequest;
use App\Services\OvertimeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OvertimeController extends Controller
{
    protected $overtimeService;

    public function __construct(OvertimeService $overtimeService)
    {
        $this->overtimeService = $overtimeService;
    }

    public function index(Request $request)
    {
        $query = OvertimeRequest::with('employee.user', 'employee.department', 'approver');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        if ($request->filled('department_id')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        $overtimes = $query->orderBy('created_at', 'desc')->paginate(15);
        $employees = Employee::active()->orderBy('full_name')->get();

        return view('overtimes.index', compact('overtimes', 'employees'));
    }

    public function create()
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda tidak terdaftar sebagai karyawan.');
        }

        return view('overtimes.create', compact('employee'));
    }

    public function store(StoreOvertimeRequest $request)
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return back()->with('error', 'Anda tidak terdaftar sebagai karyawan.');
        }

        try {
            $overtime = $this->overtimeService->submitOvertime($employee, $request->validated());

            ActivityLog::create([
                'user_id' => $user->id,
                'log_type' => 'create',
                'action' => 'submit',
                'module' => 'overtime',
                'description' => "Submitted overtime request for {$overtime->date}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'new_values' => $overtime->toArray(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
            ]);

            return redirect()->route('overtimes.my')
                ->with('success', 'Pengajuan lembur berhasil dikirim.');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $overtime = OvertimeRequest::with(['employee.user', 'employee.department', 'employee.position', 'approver'])
            ->findOrFail($id);

        return view('overtimes.show', compact('overtime'));
    }

    public function approve(ApproveOvertimeRequest $request, $id)
    {
        $overtime = OvertimeRequest::findOrFail($id);

        try {
            if ($request->status === 'approved') {
                $this->overtimeService->approveOvertime($overtime, Auth::user());
                $description = "Approved overtime request for {$overtime->date}";
            } else {
                $this->overtimeService->rejectOvertime($overtime, Auth::user(), $request->rejected_reason);
                $description = "Rejected overtime request for {$overtime->date}";
            }

            ActivityLog::create([
                'user_id' => Auth::id(),
                'log_type' => $request->status === 'approved' ? 'approve' : 'reject',
                'action' => $request->status === 'approved' ? 'approve' : 'reject',
                'module' => 'overtime',
                'description' => $description,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'new_values' => $overtime->fresh()->toArray(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
            ]);

            $message = $request->status === 'approved'
                ? 'Pengajuan lembur berhasil disetujui.'
                : 'Pengajuan lembur berhasil ditolak.';

            return redirect()->route('overtimes.index')
                ->with('success', $message);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function myOvertime(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda tidak terdaftar sebagai karyawan.');
        }

        $query = OvertimeRequest::where('employee_id', $employee->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('month')) {
            $query->whereMonth('date', $request->month);
        }

        if ($request->filled('year')) {
            $query->whereYear('date', $request->year);
        }

        $overtimes = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('overtimes.my', compact('overtimes', 'employee'));
    }
}
