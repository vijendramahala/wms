<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Demodone extends Model
{
    use HasFactory;

     protected $fillable = [
        'location_id',
        'staff_id',
        'date',
        'prospect_name',
        'product',
        'staff_name',
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
