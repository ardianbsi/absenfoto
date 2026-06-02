<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaveType extends Model
{
    use SoftDeletes;

    protected $table = 'leave_types';

    protected $fillable = [
        'name',
        'code',
        'description',
        'quota',
        'is_paid',
        'is_deduct_quota',
        'require_attachment',
        'is_active',
        'max_days',
    ];

    protected $casts = [
        'is_paid' => 'boolean',
        'is_deduct_quota' => 'boolean',
        'require_attachment' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
