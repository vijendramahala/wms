<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Target extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_id',
        'staff_id',
        'month_target',
        'month_received',
        'week_target',
        'week_received'
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
