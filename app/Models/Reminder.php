<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    protected $fillable = [
        'location_id',
        'staff_id',
        'title',
        'reminder_date',
        'reminder_time',
    ];

    // Relationships

    public function register()
    {
        return $this->belongsTo(Register::class, 'location_id', 'id');
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}
