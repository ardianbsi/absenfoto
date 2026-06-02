<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $table = 'holidays';

    protected $fillable = [
        'name',
        'date',
        'type',
        'year',
        'is_recurring',
        'description',
    ];

    protected $casts = [
        'date' => 'date',
        'is_recurring' => 'boolean',
    ];

    public function scopeByYear($query, $year)
    {
        return $query->where('year', $year);
    }

    public function scopeNational($query)
    {
        return $query->where('type', 'national');
    }
}
