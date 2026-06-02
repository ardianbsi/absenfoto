<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::withCount('employees', 'positions')
            ->orderBy('name')
            ->paginate(15);

        return view('departments.index', compact('departments'));
    }

    public function create()
    {
        return redirect()->route('departments.index');
    }

    public function show($id)
    {
        return redirect()->route('departments.index');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:departments,name'],
            'code' => ['required', 'string', 'max:20', 'unique:departments,code'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
        ]);

        $department = Department::create($data);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'log_type' => 'create',
            'action' => 'create',
            'module' => 'department',
            'description' => "Created department {$department->name}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'new_values' => $department->toArray(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
        ]);

        return redirect()->route('departments.index')
            ->with('success', 'Departemen berhasil ditambahkan.');
    }

    public function edit($id)
    {
        return redirect()->route('departments.index');
    }

    public function update(Request $request, $id)
    {
        $department = Department::findOrFail($id);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:departments,name,' . $department->id],
            'code' => ['required', 'string', 'max:20', 'unique:departments,code,' . $department->id],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
        ]);

        $oldValues = $department->toArray();
        $department->update($data);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'log_type' => 'update',
            'action' => 'update',
            'module' => 'department',
            'description' => "Updated department {$department->name}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'old_values' => $oldValues,
            'new_values' => $department->fresh()->toArray(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
        ]);

        return redirect()->route('departments.index')
            ->with('success', 'Departemen berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $department = Department::withCount('employees', 'positions')->findOrFail($id);

        if ($department->employees_count > 0 || $department->positions_count > 0) {
            return back()->with('error', 'Departemen tidak dapat dihapus karena masih memiliki karyawan atau jabatan.');
        }

        $department->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'log_type' => 'delete',
            'action' => 'delete',
            'module' => 'department',
            'description' => "Deleted department {$department->name}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('departments.index')
            ->with('success', 'Departemen berhasil dihapus.');
    }
}
