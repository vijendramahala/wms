<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Callinglog extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_id',
        'staff_id',
        'not_recieved',
        'hot_client',
        'not_required',
        'demo',
        'total_calling',
        'work_remark',
        'support',
        'support_remark',
        'installation',
        'install_remark',
        'demo_given',
        'demo_remark',
    ];

    public function location()
    {
        return $this->belongsTo(Register::class, 'location_id');
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }
}
