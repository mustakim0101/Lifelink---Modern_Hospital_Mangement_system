<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NurseVitalSignLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'admission_id',
        'patient_id',
        'nurse_id',
        'measured_at',
        'temperature_c',
        'pulse_bpm',
        'systolic_bp',
        'diastolic_bp',
        'respiration_rate',
        'spo2_percent',
        'note',
    ];

    protected $casts = [
        'measured_at' => 'datetime',
        'temperature_c' => 'decimal:1',
    ];

    public function admission(): BelongsTo
    {
        return $this->belongsTo(Admission::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }

    public function nurse(): BelongsTo
    {
        return $this->belongsTo(Nurse::class, 'nurse_id', 'nurse_id');
    }
}
