<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Leave extends Model
{
    use HasFactory;

     protected $fillable = [
        'location_id',
        'staff_id',
        'apply_date',
        'from_date',
        'to_date',
        'total_days',
        'reason',
        'approve_status',
    ];

    protected $casts = [
        'reason' => 'array', // JSON field
        'apply_date' => 'date',
        'from_date' => 'date',
        'to_date' => 'date',
    ];

    public function location()
    {
        return $this->belongsTo(Register::class, 'location_id');
    }
    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}
