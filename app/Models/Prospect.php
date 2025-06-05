<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Prospect extends Model
{

    use HasFactory;

     protected $fillable = [
        'location_id',
        'staff_id',
        'priority',
        'prospect_name',
        'mobile_no',
        'alternative_no',
        'city',
        'district',
        'state',
        'address',
        "action_taken",
        'product',
        'variant',
        'status_type',
        'software_price',
        'date',
        'time',
        'remark',
        'demo_details',
    ];

    protected static function booted()
    {
        static::updating(function ($prospect) {
            \App\Models\ProspectHistory::create([
                'prospect_id' => $prospect->id,
                'updated_by' => auth()->id(),
                'old_data' => $prospect->getOriginal(),
            ]);
        });
    }

    // ✅ Relationships
    public function location()
    {
        return $this->belongsTo(Register::class, 'location_id');
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }
}
