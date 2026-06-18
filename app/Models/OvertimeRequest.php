<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OvertimeRequest extends Model
{
    use SoftDeletes;

    protected $table = 'overtime_requests';

    protected $fillable = [
        'employee_id',
        'date',
        'start_time',
        'end_time',
        'total_minutes',
        'description',
        'attachment',
        'status',
        'approved_by',
        'approved_at',
        'rejected_reason',
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeByEmployee($query, $id)
    {
        return $query->where('employee_id', $id);
    }

    public function scopeByDateRange($query, $start, $end)
    {
        return $query->whereBetween('date', [$start, $end]);
    }
}
