<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BloodInventory extends Model
{
    use HasFactory;

    protected $table = 'blood_inventory';

    protected $fillable = [
        'blood_bank_id',
        'blood_group',
        'component_type',
        'units_available',
        'last_updated_at',
    ];

    protected $casts = [
        'units_available' => 'integer',
        'last_updated_at' => 'datetime',
    ];

    public function bloodBank(): BelongsTo
    {
        return $this->belongsTo(BloodBank::class);
    }
}
