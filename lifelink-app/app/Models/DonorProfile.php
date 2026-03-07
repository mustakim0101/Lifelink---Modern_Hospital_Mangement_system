<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DonorProfile extends Model
{
    use HasFactory;

    protected $primaryKey = 'donor_id';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'donor_id',
        'blood_group',
        'last_donation_date',
        'is_eligible',
        'notes',
    ];

    protected $casts = [
        'last_donation_date' => 'datetime',
        'is_eligible' => 'boolean',
    ];

    public function donor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'donor_id');
    }

    public function availabilities(): HasMany
    {
        return $this->hasMany(DonorAvailability::class, 'donor_id', 'donor_id');
    }

    public function healthChecks(): HasMany
    {
        return $this->hasMany(DonorHealthCheck::class, 'donor_id', 'donor_id');
    }

    public function donations(): HasMany
    {
        return $this->hasMany(BloodDonation::class, 'donor_id', 'donor_id');
    }
}
