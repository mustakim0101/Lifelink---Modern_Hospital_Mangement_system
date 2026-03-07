<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Admission extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_user_id',
        'department_id',
        'admitted_by_doctor_id',
        'diagnosis',
        'care_level_requested',
        'care_level_assigned',
        'status',
        'admit_date',
        'discharge_date',
        'notes',
    ];

    protected $casts = [
        'admit_date' => 'datetime',
        'discharge_date' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_user_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function admittedByDoctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admitted_by_doctor_id');
    }

    public function bedAssignments(): HasMany
    {
        return $this->hasMany(BedAssignment::class);
    }

    public function medicalRecords(): HasMany
    {
        return $this->hasMany(MedicalRecord::class);
    }
}
