<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkCalendar extends Model
{
    protected $table = 'work_calendars';

    protected $fillable = [
        'name',
        'date',
        'type',
        'is_holiday',
        'description',
    ];

    protected $casts = [
        'date' => 'date',
        'is_holiday' => 'boolean',
    ];

    public function scopeHoliday($query)
    {
        return $query->where('is_holiday', true);
    }

    public function scopeWorkDay($query)
    {
        return $query->where('is_holiday', false);
    }
}
