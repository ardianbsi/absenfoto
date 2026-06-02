<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\ActivityLog;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with(['user', 'department', 'position', 'manager']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('work_status')) {
            $query->where('work_status', $request->work_status);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }

        $employees = $query->orderBy('full_name')->paginate(15);
        $departments = Department::active()->orderBy('name')->get();
        $positions = Position::active()->orderBy('name')->get();

        return view('employees.index', compact('employees', 'departments', 'positions'));
    }

    public function create()
    {
        $departments = Department::active()->orderBy('name')->get();
        $positions = Position::active()->orderBy('name')->get();
        $managers = Employee::active()->whereDoesntHave('user', function ($q) {
            $q->whereHas('role', function ($r) {
                $r->where('name', 'employee');
            });
        })->orderBy('full_name')->get();

        return view('employees.create', compact('departments', 'positions', 'managers'));
    }

    public function store(StoreEmployeeRequest $request)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $request) {
            $username = explode('@', $data['email'])[0];
            $username = Str::slug($username, '.');

            $user = User::create([
                'name' => $data['full_name'],
                'username' => $username,
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => Hash::make('password123'),
                'is_active' => true,
                'role_id' => 4,
            ]);

            $photoPath = null;
            if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
                $photoPath = $request->file('photo')->store('employees/photo', 'public');
            }

            $employee = Employee::create([
                'user_id' => $user->id,
                'nik' => $data['nik'],
                'full_name' => $data['full_name'],
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'place_of_birth' => $data['place_of_birth'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'gender' => $data['gender'],
                'department_id' => $data['department_id'],
                'position_id' => $data['position_id'],
                'manager_id' => $data['manager_id'] ?? null,
                'join_date' => $data['join_date'],
                'work_status' => $data['work_status'],
                'shift_id' => $data['shift_id'] ?? null,
                'photo' => $photoPath,
                'is_active' => true,
            ]);

            ActivityLog::create([
                'user_id' => Auth::id(),
                'log_type' => 'create',
                'action' => 'create',
                'module' => 'employee',
                'description' => "Created employee {$employee->full_name}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'new_values' => $employee->toArray(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
            ]);
        });

        return redirect()->route('employees.index')
            ->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function show($id)
    {
        $employee = Employee::with(['user', 'department', 'position', 'manager', 'attendances' => function ($q) {
            $q->latest()->limit(30);
        }, 'leaveRequests' => function ($q) {
            $q->latest()->limit(10);
        }, 'overtimeRequests' => function ($q) {
            $q->latest()->limit(10);
        }])->findOrFail($id);

        return view('employees.show', compact('employee'));
    }

    public function edit($id)
    {
        $employee = Employee::with('user')->findOrFail($id);
        $departments = Department::active()->orderBy('name')->get();
        $positions = Position::active()->orderBy('name')->get();
        $managers = Employee::active()->where('id', '!=', $employee->id)
            ->whereDoesntHave('user', function ($q) {
                $q->whereHas('role', function ($r) {
                    $r->where('name', 'employee');
                });
            })->orderBy('full_name')->get();

        return view('employees.edit', compact('employee', 'departments', 'positions', 'managers'));
    }

    public function update(UpdateEmployeeRequest $request, $id)
    {
        $employee = Employee::with('user')->findOrFail($id);
        $data = $request->validated();

        DB::transaction(function () use ($data, $request, $employee) {
            $oldValues = $employee->toArray();

            if ($employee->user) {
                $employee->user->update([
                    'name' => $data['full_name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'] ?? null,
                ]);
            }

            $photoPath = $employee->photo;
            if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
                $photoPath = $request->file('photo')->store('employees/photo', 'public');
            }

            $employee->update([
                'nik' => $data['nik'],
                'full_name' => $data['full_name'],
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'place_of_birth' => $data['place_of_birth'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'gender' => $data['gender'],
                'department_id' => $data['department_id'],
                'position_id' => $data['position_id'],
                'manager_id' => $data['manager_id'] ?? null,
                'join_date' => $data['join_date'],
                'work_status' => $data['work_status'],
                'shift_id' => $data['shift_id'] ?? null,
                'photo' => $photoPath,
            ]);

            ActivityLog::create([
                'user_id' => Auth::id(),
                'log_type' => 'update',
                'action' => 'update',
                'module' => 'employee',
                'description' => "Updated employee {$employee->full_name}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'old_values' => $oldValues,
                'new_values' => $employee->fresh()->toArray(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
            ]);
        });

        return redirect()->route('employees.index')
            ->with('success', 'Karyawan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);

        DB::transaction(function () use ($employee) {
            $employee->update(['is_active' => false]);
            $employee->delete();

            if ($employee->user) {
                $employee->user->update(['is_active' => false]);
                $employee->user->delete();
            }

            ActivityLog::create([
                'user_id' => Auth::id(),
                'log_type' => 'delete',
                'action' => 'delete',
                'module' => 'employee',
                'description' => "Deleted employee {$employee->full_name}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });

        return redirect()->route('employees.index')
            ->with('success', 'Karyawan berhasil dihapus.');
    }
}
