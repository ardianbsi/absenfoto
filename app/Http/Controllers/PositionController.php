<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PositionController extends Controller
{
    public function index(Request $request)
    {
        $query = Position::with('department', 'department.employees')
            ->withCount('employees')
            ->orderBy('name');

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        $positions = $query->paginate(15);
        $departments = Department::active()->orderBy('name')->get();

        return view('positions.index', compact('positions', 'departments'));
    }

    public function create()
    {
        return redirect()->route('positions.index');
    }

    public function show($id)
    {
        return redirect()->route('positions.index');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
        ]);

        $position = Position::create($data);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'log_type' => 'create',
            'action' => 'create',
            'module' => 'position',
            'description' => "Created position {$position->name}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'new_values' => $position->toArray(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
        ]);

        return redirect()->route('positions.index')
            ->with('success', 'Jabatan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        return redirect()->route('positions.index');
    }

    public function update(Request $request, $id)
    {
        $position = Position::findOrFail($id);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
        ]);

        $oldValues = $position->toArray();
        $position->update($data);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'log_type' => 'update',
            'action' => 'update',
            'module' => 'position',
            'description' => "Updated position {$position->name}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'old_values' => $oldValues,
            'new_values' => $position->fresh()->toArray(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
        ]);

        return redirect()->route('positions.index')
            ->with('success', 'Jabatan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $position = Position::withCount('employees')->findOrFail($id);

        if ($position->employees_count > 0) {
            return back()->with('error', 'Jabatan tidak dapat dihapus karena masih memiliki karyawan.');
        }

        $position->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'log_type' => 'delete',
            'action' => 'delete',
            'module' => 'position',
            'description' => "Deleted position {$position->name}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('positions.index')
            ->with('success', 'Jabatan berhasil dihapus.');
    }
}
