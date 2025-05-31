<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Staff extends Model
{
    use HasFactory;

    protected $table = 'staffs';

     protected $fillable = [
    'staff_name',
    'father_name',
    'mobile_no',
    'pin_code',
    'state',
    'city',
    'address',
    'sales_man',
    'sales_executive',
    'password',
    'joining_date',
    'resignation_date',
    'user_id',
];

    // Date fields
    protected $casts = [
        'joining_date' => 'date',
        'resignation_date' => 'date', // ✅ ye line ho to achha rahega
    ];
}
