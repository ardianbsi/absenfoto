<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;

class AttendanceObserver
{
    public function created(Attendance $attendance): void
    {
        ActivityLog::create([
            'user_id'    => Auth::id(),
            'log_type'   => 'info',
            'action'     => 'create',
            'module'     => 'attendance',
            'description' => 'Absensi dibuat: ' . ($attendance->employee?->full_name ?? 'Unknown') . ' - ' . $attendance->date,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'new_values' => $attendance->toArray(),
            'url'        => request()->fullUrl(),
            'method'     => request()->method(),
        ]);
    }

    public function updated(Attendance $attendance): void
    {
        $changed = $attendance->getChanges();
        unset($changed['updated_at']);

        if (empty($changed)) {
            return;
        }

        $original = [];
        foreach ($changed as $key => $value) {
            $original[$key] = $attendance->getOriginal($key);
        }

        ActivityLog::create([
            'user_id'    => Auth::id(),
            'log_type'   => 'warning',
            'action'     => 'update',
            'module'     => 'attendance',
            'description' => 'Absensi diubah: ' . ($attendance->employee?->full_name ?? 'Unknown') . ' - ' . $attendance->date,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'old_values' => $original,
            'new_values' => $changed,
            'url'        => request()->fullUrl(),
            'method'     => request()->method(),
        ]);
    }

    public function deleted(Attendance $attendance): void
    {
        ActivityLog::create([
            'user_id'    => Auth::id(),
            'log_type'   => 'danger',
            'action'     => 'delete',
            'module'     => 'attendance',
            'description' => 'Absensi dihapus: ' . ($attendance->employee?->full_name ?? 'Unknown') . ' - ' . $attendance->date,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'old_values' => $attendance->toArray(),
            'url'        => request()->fullUrl(),
            'method'     => request()->method(),
        ]);
    }
}
