<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notice extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'location_id',
        'staff_id', // store JSON array like [1,2,3]
        'title',
        'subtitle',
        'note',
    ];

    // Automatically cast staff_id to array when accessing
    protected $casts = [
        'staff_id' => 'array',
    ];

    public function register()
    {
        return $this->belongsTo(Register::class, 'location_id', 'id');
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}
