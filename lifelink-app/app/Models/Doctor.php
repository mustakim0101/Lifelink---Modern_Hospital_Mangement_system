<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Doctor extends Model
{
    use HasFactory;

    protected $primaryKey = 'doctor_id';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'doctor_id',
        'department_id',
        'specialization',
        'license_number',
        'years_experience',
        'consultation_fee',
        'bio',
        'profile_image_url',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'years_experience' => 'integer',
        'consultation_fee' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function appointmentRules(): HasMany
    {
        return $this->hasMany(DoctorAppointmentRule::class, 'doctor_user_id', 'doctor_id');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'doctor_user_id', 'doctor_id');
    }

    public function doctorReviews(): HasMany
    {
        return $this->hasMany(DoctorReview::class, 'doctor_user_id', 'doctor_id');
    }
}
