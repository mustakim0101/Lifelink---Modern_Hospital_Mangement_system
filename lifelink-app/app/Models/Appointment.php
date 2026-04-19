<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'department_id',
        'doctor_user_id',
        'appointment_date',
        'appointment_datetime',
        'status',
        'approved_by_user_id',
        'approved_at',
        'rejection_reason',
        'cancelled_by_user_id',
        'cancel_reason',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'appointment_datetime' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_user_id');
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by_user_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    public function doctorReviews(): HasMany
    {
        return $this->hasMany(DoctorReview::class, 'appointment_id');
    }
}
