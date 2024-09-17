<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Carbon;

class Project_user extends Pivot
{
    protected $fillable = ['role', 'last_activity', 'distribution_hours'];

    protected $casts = [
        'distribution_hours' => 'integer',
        'last_activity' => 'datetime',
    ];

    public function getLastActivityAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    public function setLastActivityAttribute($value)
    {
        $this->attributes['last_activity'] = Carbon::parse($value)->toDateTimeString();
    }

    public function updateDistributionHours($hours)
    {
        $this->update(['distribution_hours' => $hours]);
    }
}
