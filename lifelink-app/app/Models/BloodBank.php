<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BloodBank extends Model
{
    use HasFactory;

    protected $fillable = [
        'bank_name',
        'location',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function inventoryRows(): HasMany
    {
        return $this->hasMany(BloodInventory::class);
    }

    public function bloodRequests(): HasMany
    {
        return $this->hasMany(BloodRequest::class);
    }

    public function donations(): HasMany
    {
        return $this->hasMany(BloodDonation::class);
    }
}
