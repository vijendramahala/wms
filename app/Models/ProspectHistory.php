<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProspectHistory extends Model
{
    protected $fillable = ['prospect_id', 'updated_by', 'old_data'];

    protected $casts = [
        'old_data' => 'array',
    ];

    public function prospect()
    {
        return $this->belongsTo(Prospect::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
