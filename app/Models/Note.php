<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Note extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_id',
        'staff_id',
        'title',
        'note',
        'background_color',
        'task',
        'pin_status',
    ];

    // 👇 Type casting for JSON and boolean fields
    protected $casts = [
        'task' => 'array',
        'pin_status' => 'boolean',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function location()
    {
        return $this->belongsTo(Register::class, 'location_id');
    }
}
