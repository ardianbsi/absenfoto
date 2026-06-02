<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApproveLeaveRequest;
use App\Http\Requests\StoreLeaveRequest;
use App\Models\ActivityLog;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Services\LeaveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    protected $leaveService;

    public function __construct(LeaveService $leaveService)
    {
        $this->leaveService = $leaveService;
    }

    public function index(Request $request)
    {
        $query = LeaveRequest::with('employee.user', 'employee.department', 'leaveType', 'approver');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('leave_type_id')) {
            $query->where('leave_type_id', $request->leave_type_id);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('start_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('end_date', '<=', $request->end_date);
        }

        if ($request->filled('department_id')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        $leaves = $query->orderBy('created_at', 'desc')->paginate(15);
        $leaveTypes = LeaveType::active()->orderBy('name')->get();
        $employees = Employee::active()->orderBy('full_name')->get();

        return view('leaves.index', compact('leaves', 'leaveTypes', 'employees'));
    }

    public function create()
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda tidak terdaftar sebagai karyawan.');
        }

        $leaveTypes = LeaveType::active()->orderBy('name')->get();
        $balances = [];
        foreach ($leaveTypes as $leaveType) {
            $balances[$leaveType->id] = $this->leaveService->getLeaveBalance($employee, $leaveType);
        }

        return view('leaves.create', compact('leaveTypes', 'employee', 'balances'));
    }

    public function store(StoreLeaveRequest $request)
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return back()->with('error', 'Anda tidak terdaftar sebagai karyawan.');
        }

        try {
            $leave = $this->leaveService->submitLeave($employee, $request->validated());

            ActivityLog::create([
                'user_id' => $user->id,
                'log_type' => 'create',
                'action' => 'submit',
                'module' => 'leave',
                'description' => "Submitted leave request {$leave->leaveType?->name}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'new_values' => $leave->toArray(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
            ]);

            return redirect()->route('leaves.my')
                ->with('success', 'Pengajuan cuti/izin berhasil dikirim.');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $leave = LeaveRequest::with(['employee.user', 'employee.department', 'employee.position', 'leaveType', 'approver'])
            ->findOrFail($id);

        return view('leaves.show', compact('leave'));
    }

    public function approve(ApproveLeaveRequest $request, $id)
    {
        $leave = LeaveRequest::findOrFail($id);

        try {
            if ($request->status === 'approved') {
                $this->leaveService->approveLeave($leave, Auth::user());
                $description = "Approved leave request {$leave->leaveType?->name}";
            } else {
                $this->leaveService->rejectLeave($leave, Auth::user(), $request->rejected_reason);
                $description = "Rejected leave request {$leave->leaveType?->name}";
            }

            ActivityLog::create([
                'user_id' => Auth::id(),
                'log_type' => $request->status === 'approved' ? 'approve' : 'reject',
                'action' => $request->status === 'approved' ? 'approve' : 'reject',
                'module' => 'leave',
                'description' => $description,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'new_values' => $leave->fresh()->toArray(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
            ]);

            $message = $request->status === 'approved'
                ? 'Pengajuan cuti/izin berhasil disetujui.'
                : 'Pengajuan cuti/izin berhasil ditolak.';

            return redirect()->route('leaves.index')
                ->with('success', $message);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function myLeave(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda tidak terdaftar sebagai karyawan.');
        }

        $query = LeaveRequest::with('leaveType')
            ->where('employee_id', $employee->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('year')) {
            $query->whereYear('start_date', $request->year);
        }

        $leaves = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('leaves.my', compact('leaves', 'employee'));
    }

    public function balance()
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda tidak terdaftar sebagai karyawan.');
        }

        $leaveTypes = LeaveType::active()->orderBy('name')->get();
        $balances = [];

        foreach ($leaveTypes as $leaveType) {
            $used = LeaveRequest::where('employee_id', $employee->id)
                ->where('leave_type_id', $leaveType->id)
                ->whereIn('status', ['approved', 'pending'])
                ->whereYear('start_date', now()->year)
                ->sum('total_days');

            $balances[] = [
                'type' => $leaveType,
                'quota' => $leaveType->quota,
                'used' => $used,
                'remaining' => max(0, $leaveType->quota - $used),
            ];
        }

        return view('leaves.balance', compact('employee', 'balances'));
    }
}
