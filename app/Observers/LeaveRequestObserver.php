<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\LeaveRequest;
use Illuminate\Support\Facades\Auth;

class LeaveRequestObserver
{
    public function created(LeaveRequest $leaveRequest): void
    {
        ActivityLog::create([
            'user_id'    => Auth::id(),
            'log_type'   => 'info',
            'action'     => 'create',
            'module'     => 'leave',
            'description' => 'Pengajuan cuti dibuat: ' . ($leaveRequest->employee?->full_name ?? 'Unknown') . ' - ' . ($leaveRequest->leaveType?->name ?? 'Unknown'),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'new_values' => $leaveRequest->toArray(),
            'url'        => request()->fullUrl(),
            'method'     => request()->method(),
        ]);
    }

    public function updated(LeaveRequest $leaveRequest): void
    {
        $changed = $leaveRequest->getChanges();
        unset($changed['updated_at']);

        if (empty($changed)) {
            return;
        }

        $original = [];
        foreach ($changed as $key => $value) {
            $original[$key] = $leaveRequest->getOriginal($key);
        }

        $description = 'Pengajuan cuti diubah: ' . ($leaveRequest->employee?->full_name ?? 'Unknown');

        if (isset($changed['status'])) {
            $description = 'Status pengajuan cuti menjadi ' . $changed['status'] . ': ' . ($leaveRequest->employee?->full_name ?? 'Unknown');
        }

        ActivityLog::create([
            'user_id'    => Auth::id(),
            'log_type'   => isset($changed['status']) ? 'warning' : 'info',
            'action'     => 'update',
            'module'     => 'leave',
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'old_values' => $original,
            'new_values' => $changed,
            'url'        => request()->fullUrl(),
            'method'     => request()->method(),
        ]);
    }

    public function deleted(LeaveRequest $leaveRequest): void
    {
        ActivityLog::create([
            'user_id'    => Auth::id(),
            'log_type'   => 'danger',
            'action'     => 'delete',
            'module'     => 'leave',
            'description' => 'Pengajuan cuti dihapus: ' . ($leaveRequest->employee?->full_name ?? 'Unknown'),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'old_values' => $leaveRequest->toArray(),
            'url'        => request()->fullUrl(),
            'method'     => request()->method(),
        ]);
    }
}
