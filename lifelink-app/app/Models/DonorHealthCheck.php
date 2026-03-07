<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DonorHealthCheck extends Model
{
    use HasFactory;

    protected $fillable = [
        'donor_id',
        'check_datetime',
        'weight_kg',
        'temperature_c',
        'hemoglobin',
        'notes',
        'checked_by_user_id',
    ];

    protected $casts = [
        'check_datetime' => 'datetime',
        'weight_kg' => 'decimal:2',
        'temperature_c' => 'decimal:2',
        'hemoglobin' => 'decimal:2',
    ];

    public function donorProfile(): BelongsTo
    {
        return $this->belongsTo(DonorProfile::class, 'donor_id', 'donor_id');
    }

    public function checkedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_by_user_id');
    }
}
