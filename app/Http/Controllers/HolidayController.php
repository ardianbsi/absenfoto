<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Holiday;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HolidayController extends Controller
{
    public function index(Request $request)
    {
        $query = Holiday::orderBy('date', 'desc');

        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $query->where('year', $year);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $prevMonth = $month == 1 ? 12 : $month - 1;
        $prevYear = $month == 1 ? $year - 1 : $year;
        $nextMonth = $month == 12 ? 1 : $month + 1;
        $nextYear = $month == 12 ? $year + 1 : $year;
        $currentMonthName = \Carbon\Carbon::create()->month($month)->monthName;
        $currentMonth = $month;
        $currentYear = $year;
        $firstDayOfMonth = \Carbon\Carbon::create($year, $month, 1)->dayOfWeek;
        $daysInMonth = \Carbon\Carbon::create($year, $month, 1)->daysInMonth;

        $holidays = $query->paginate(15);
        $years = Holiday::selectRaw('DISTINCT year')->orderBy('year', 'desc')->pluck('year');

        return view('holidays.index', compact(
            'holidays', 'years',
            'prevMonth', 'prevYear', 'nextMonth', 'nextYear',
            'currentMonthName', 'currentMonth', 'currentYear',
            'firstDayOfMonth', 'daysInMonth'
        ));
    }

    public function create()
    {
        return redirect()->route('holidays.index');
    }

    public function show($id)
    {
        return redirect()->route('holidays.index');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'type' => ['required', 'string', 'in:national,company,optional'],
            'year' => ['required', 'integer', 'min:2020', 'max:2099'],
            'is_recurring' => ['boolean'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $holiday = Holiday::create($data);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'log_type' => 'create',
            'action' => 'create',
            'module' => 'holiday',
            'description' => "Created holiday {$holiday->name}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'new_values' => $holiday->toArray(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
        ]);

        return redirect()->route('holidays.index')
            ->with('success', 'Hari libur berhasil ditambahkan.');
    }

    public function edit($id)
    {
        return redirect()->route('holidays.index');
    }

    public function update(Request $request, $id)
    {
        $holiday = Holiday::findOrFail($id);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'type' => ['required', 'string', 'in:national,company,optional'],
            'year' => ['required', 'integer', 'min:2020', 'max:2099'],
            'is_recurring' => ['boolean'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $oldValues = $holiday->toArray();
        $holiday->update($data);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'log_type' => 'update',
            'action' => 'update',
            'module' => 'holiday',
            'description' => "Updated holiday {$holiday->name}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'old_values' => $oldValues,
            'new_values' => $holiday->fresh()->toArray(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
        ]);

        return redirect()->route('holidays.index')
            ->with('success', 'Hari libur berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $holiday = Holiday::findOrFail($id);
        $holiday->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'log_type' => 'delete',
            'action' => 'delete',
            'module' => 'holiday',
            'description' => "Deleted holiday {$holiday->name}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('holidays.index')
            ->with('success', 'Hari libur berhasil dihapus.');
    }
}
