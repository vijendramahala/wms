<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Misc extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'location_id',
        'external_id',
        'name'
    ];

    public function location()
    {
        return $this->belongsTo(Register::class, 'location_id');
    }
}
