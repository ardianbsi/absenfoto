<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Announcement;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $query = Announcement::with('creator', 'department')->latest();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('is_published')) {
            $query->where('is_published', filter_var($request->is_published, FILTER_VALIDATE_BOOLEAN));
        }

        $announcements = $query->paginate(15);
        $departments = Department::active()->orderBy('name')->get();

        return view('announcements.index', compact('announcements', 'departments'));
    }

    public function create()
    {
        return redirect()->route('announcements.index');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'type' => ['required', 'string', 'in:general,department,urgent'],
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'is_published' => ['boolean'],
            'published_at' => ['nullable', 'date'],
        ]);

        $data['created_by'] = Auth::id();
        $data['published_at'] = $data['is_published'] ?? false ? now() : ($data['published_at'] ?? null);

        $announcement = Announcement::create($data);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'log_type' => 'create',
            'action' => 'create',
            'module' => 'announcement',
            'description' => "Created announcement {$announcement->title}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'new_values' => $announcement->toArray(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
        ]);

        return redirect()->route('announcements.index')
            ->with('success', 'Pengumuman berhasil ditambahkan.');
    }

    public function show($id)
    {
        $announcement = Announcement::with('creator', 'department')->findOrFail($id);

        return view('announcements.show', compact('announcement'));
    }

    public function edit($id)
    {
        return redirect()->route('announcements.index');
    }

    public function update(Request $request, $id)
    {
        $announcement = Announcement::findOrFail($id);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'type' => ['required', 'string', 'in:general,department,urgent'],
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'is_published' => ['boolean'],
            'published_at' => ['nullable', 'date'],
        ]);

        if (($data['is_published'] ?? false) && !$announcement->is_published) {
            $data['published_at'] = now();
        }

        $oldValues = $announcement->toArray();
        $announcement->update($data);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'log_type' => 'update',
            'action' => 'update',
            'module' => 'announcement',
            'description' => "Updated announcement {$announcement->title}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'old_values' => $oldValues,
            'new_values' => $announcement->fresh()->toArray(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
        ]);

        return redirect()->route('announcements.index')
            ->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'log_type' => 'delete',
            'action' => 'delete',
            'module' => 'announcement',
            'description' => "Deleted announcement {$announcement->title}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('announcements.index')
            ->with('success', 'Pengumuman berhasil dihapus.');
    }
}
