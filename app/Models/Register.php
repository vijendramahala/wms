<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Register extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'owner_name',
        'business_type',
        'address',
        'phone_no',
        'email',
        'password',
    ];

    // Hide password from JSON responses (optional but recommended)
    protected $hidden = [
        'password',
    ];
}
