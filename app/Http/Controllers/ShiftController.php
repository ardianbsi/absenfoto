<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreShiftRequest;
use App\Models\ActivityLog;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Shift;
use App\Models\ShiftSchedule;
use App\Services\ShiftService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShiftController extends Controller
{
    protected $shiftService;

    public function __construct(ShiftService $shiftService)
    {
        $this->shiftService = $shiftService;
    }

    public function index()
    {
        $shifts = Shift::withCount('employees')->orderBy('name')->paginate(15);

        return view('shifts.index', compact('shifts'));
    }

    public function create()
    {
        return view('shifts.create');
    }

    public function store(StoreShiftRequest $request)
    {
        $data = $request->validated();

        $shift = Shift::create($data);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'log_type' => 'create',
            'action' => 'create',
            'module' => 'shift',
            'description' => "Created shift {$shift->name}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'new_values' => $shift->toArray(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
        ]);

        return redirect()->route('shifts.index')
            ->with('success', 'Shift berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $shift = Shift::findOrFail($id);

        return view('shifts.edit', compact('shift'));
    }

    public function update(Request $request, $id)
    {
        $shift = Shift::findOrFail($id);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:20', 'unique:shifts,code,' . $shift->id],
            'type' => ['required', 'string', 'in:fixed,flexible,rotating'],
            'color' => ['nullable', 'string', 'max:7'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required_if:type,fixed', 'date_format:H:i', 'after:start_time'],
            'tolerance_minutes' => ['nullable', 'integer', 'min:0', 'max:120'],
            'max_late_minutes' => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
        ]);

        $oldValues = $shift->toArray();
        $shift->update($data);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'log_type' => 'update',
            'action' => 'update',
            'module' => 'shift',
            'description' => "Updated shift {$shift->name}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'old_values' => $oldValues,
            'new_values' => $shift->fresh()->toArray(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
        ]);

        return redirect()->route('shifts.index')
            ->with('success', 'Shift berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $shift = Shift::findOrFail($id);

        $shift->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'log_type' => 'delete',
            'action' => 'delete',
            'module' => 'shift',
            'description' => "Deleted shift {$shift->name}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('shifts.index')
            ->with('success', 'Shift berhasil dihapus.');
    }

    public function assignForm()
    {
        $shifts = Shift::active()->orderBy('name')->get();
        $departments = Department::active()->orderBy('name')->get();
        $employees = Employee::active()->orderBy('full_name')->get();

        return view('shifts.assign', compact('shifts', 'departments', 'employees'));
    }

    public function assign(Request $request)
    {
        $data = $request->validate([
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'shift_id' => ['required', 'integer', 'exists:shifts,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $employee = Employee::findOrFail($data['employee_id']);
        $shift = Shift::findOrFail($data['shift_id']);

        $this->shiftService->assignShift($employee, $shift, $data['start_date'], $data['end_date'] ?? null);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'log_type' => 'assign',
            'action' => 'assign',
            'module' => 'shift',
            'description' => "Assigned shift {$shift->name} to {$employee->full_name}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'new_values' => $data,
            'url' => $request->fullUrl(),
            'method' => $request->method(),
        ]);

        return redirect()->route('shifts.assign.form')
            ->with('success', 'Shift berhasil ditugaskan.');
    }

    public function assignMass(Request $request)
    {
        $data = $request->validate([
            'employee_ids' => ['required', 'array'],
            'employee_ids.*' => ['integer', 'exists:employees,id'],
            'shift_id' => ['required', 'integer', 'exists:shifts,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $shift = Shift::findOrFail($data['shift_id']);

        $count = $this->shiftService->assignShiftMass(
            $data['employee_ids'],
            $shift,
            $data['start_date'],
            $data['end_date'] ?? null
        );

        ActivityLog::create([
            'user_id' => Auth::id(),
            'log_type' => 'assign',
            'action' => 'assign_mass',
            'module' => 'shift',
            'description' => "Mass assigned shift {$shift->name} to {$count} employees",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'new_values' => $data,
            'url' => $request->fullUrl(),
            'method' => $request->method(),
        ]);

        return redirect()->route('shifts.assign.form')
            ->with('success', "Shift berhasil ditugaskan ke {$count} karyawan.");
    }

    public function schedule(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);
        $employees = Employee::active()->with('shift')->orderBy('full_name')->get();
        $shifts = Shift::active()->orderBy('name')->get();

        return view('shifts.schedule', compact('month', 'year', 'employees', 'shifts'));
    }

    public function getScheduleData(Request $request)
    {
        $request->validate([
            'month' => ['required', 'integer', 'between:1,12'],
            'year' => ['required', 'integer', 'min:2020', 'max:2099'],
            'employee_id' => ['nullable', 'integer', 'exists:employees,id'],
        ]);

        $month = $request->input('month');
        $year = $request->input('year');
        $employeeId = $request->input('employee_id');

        $startDate = Carbon::create($year, $month, 1)->toDateString();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth()->toDateString();

        $query = ShiftSchedule::with(['employee', 'shift'])
            ->whereBetween('date', [$startDate, $endDate]);

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        $schedules = $query->orderBy('date')->get()->map(function ($schedule) {
            return [
                'id' => $schedule->id,
                'title' => $schedule->employee->full_name . ' - ' . $schedule->shift->name,
                'start' => $schedule->date->format('Y-m-d'),
                'end' => $schedule->date->format('Y-m-d'),
                'backgroundColor' => $schedule->shift->color ?? '#3788d8',
                'borderColor' => $schedule->shift->color ?? '#3788d8',
                'extendedProps' => [
                    'employee_id' => $schedule->employee_id,
                    'employee_name' => $schedule->employee->full_name,
                    'shift_name' => $schedule->shift->name,
                    'start_time' => $schedule->shift->start_time?->format('H:i'),
                    'end_time' => $schedule->shift->end_time?->format('H:i'),
                    'is_override' => $schedule->is_override,
                ],
            ];
        });

        return response()->json($schedules);
    }
}
