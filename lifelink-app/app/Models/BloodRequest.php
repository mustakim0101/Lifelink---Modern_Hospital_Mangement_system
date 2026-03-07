<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BloodRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'admission_id',
        'department_id',
        'blood_bank_id',
        'requested_by_user_id',
        'blood_group_needed',
        'component_type',
        'units_required',
        'urgency',
        'status',
        'request_date',
        'notes',
    ];

    protected $casts = [
        'request_date' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }

    public function admission(): BelongsTo
    {
        return $this->belongsTo(Admission::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function bloodBank(): BelongsTo
    {
        return $this->belongsTo(BloodBank::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by_user_id');
    }
}
