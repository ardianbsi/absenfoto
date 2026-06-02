<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayrollComponent extends Model
{
    use SoftDeletes;

    protected $table = 'payroll_components';

    protected $fillable = [
        'name',
        'code',
        'type',
        'calculation_type',
        'value',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'value' => 'decimal:2',
    ];

    public function employeePayrolls(): HasMany
    {
        return $this->hasMany(EmployeePayroll::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAllowance($query)
    {
        return $query->where('type', 'allowance');
    }

    public function scopeDeduction($query)
    {
        return $query->where('type', 'deduction');
    }
}
