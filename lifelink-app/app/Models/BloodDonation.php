<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BloodDonation extends Model
{
    use HasFactory;

    protected $fillable = [
        'donor_id',
        'blood_bank_id',
        'donation_datetime',
        'blood_group',
        'component_type',
        'units_donated',
        'recorded_by_user_id',
        'linked_request_id',
        'donor_health_check_id',
        'notes',
    ];

    protected $casts = [
        'donation_datetime' => 'datetime',
        'units_donated' => 'integer',
    ];

    public function donorProfile(): BelongsTo
    {
        return $this->belongsTo(DonorProfile::class, 'donor_id', 'donor_id');
    }

    public function bloodBank(): BelongsTo
    {
        return $this->belongsTo(BloodBank::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by_user_id');
    }

    public function linkedRequest(): BelongsTo
    {
        return $this->belongsTo(BloodRequest::class, 'linked_request_id');
    }

    public function donorHealthCheck(): BelongsTo
    {
        return $this->belongsTo(DonorHealthCheck::class, 'donor_health_check_id');
    }
}
