<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserObserver
{
    public function created(User $user): void
    {
        ActivityLog::create([
            'user_id'    => Auth::id() ?? $user->id,
            'log_type'   => 'info',
            'action'     => 'create',
            'module'     => 'user',
            'description' => 'User dibuat: ' . $user->name . ' (' . $user->email . ')',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'new_values' => $user->toArray(),
            'url'        => request()->fullUrl(),
            'method'     => request()->method(),
        ]);
    }

    public function updated(User $user): void
    {
        $changed = $user->getChanges();
        unset($changed['updated_at']);

        if (empty($changed)) {
            return;
        }

        $original = [];
        foreach ($changed as $key => $value) {
            $original[$key] = $user->getOriginal($key);
        }

        ActivityLog::create([
            'user_id'    => Auth::id(),
            'log_type'   => 'warning',
            'action'     => 'update',
            'module'     => 'user',
            'description' => 'User diubah: ' . $user->name . ' (' . $user->email . ')',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'old_values' => $original,
            'new_values' => $changed,
            'url'        => request()->fullUrl(),
            'method'     => request()->method(),
        ]);
    }

    public function deleted(User $user): void
    {
        ActivityLog::create([
            'user_id'    => Auth::id(),
            'log_type'   => 'danger',
            'action'     => 'delete',
            'module'     => 'user',
            'description' => 'User dihapus: ' . $user->name . ' (' . $user->email . ')',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'old_values' => $user->toArray(),
            'url'        => request()->fullUrl(),
            'method'     => request()->method(),
        ]);
    }
}
