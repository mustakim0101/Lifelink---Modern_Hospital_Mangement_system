<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DonorAvailability extends Model
{
    use HasFactory;

    protected $fillable = [
        'donor_id',
        'week_start_date',
        'is_available',
        'max_bags_possible',
        'notes',
    ];

    protected $casts = [
        'week_start_date' => 'date',
        'is_available' => 'boolean',
        'max_bags_possible' => 'integer',
    ];

    public function donorProfile(): BelongsTo
    {
        return $this->belongsTo(DonorProfile::class, 'donor_id', 'donor_id');
    }
}
