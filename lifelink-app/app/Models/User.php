<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'full_name',
        'email',
        'phone',
        'date_of_birth',
        'gender',
        'account_status',
        'frozen_at',
        'frozen_by_user_id',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'frozen_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles')
            ->withPivot(['assigned_at', 'assigned_by_user_id']);
    }

    public function departmentAdminScopes(): HasMany
    {
        return $this->hasMany(DepartmentAdmin::class);
    }

    public function admissionsAsPatient(): HasMany
    {
        return $this->hasMany(Admission::class, 'patient_user_id');
    }

    public function patientProfile(): HasOne
    {
        return $this->hasOne(Patient::class, 'patient_id');
    }

    public function doctorProfile(): HasOne
    {
        return $this->hasOne(Doctor::class, 'doctor_id');
    }

    public function nurseProfile(): HasOne
    {
        return $this->hasOne(Nurse::class, 'nurse_id');
    }

    public function doctorAppointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'doctor_user_id');
    }

    public function cancelledAppointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'cancelled_by_user_id');
    }

    public function createdMedicalRecords(): HasMany
    {
        return $this->hasMany(MedicalRecord::class, 'created_by_user_id');
    }

    public function recordedVitalSigns(): HasMany
    {
        return $this->hasMany(NurseVitalSignLog::class, 'nurse_id');
    }

    public function hasRole(string ...$roles): bool
    {
        if (empty($roles)) {
            return false;
        }

        return $this->roles()->whereIn('role_name', $roles)->exists();
    }

    public function isFrozen(): bool
    {
        return $this->account_status === 'Frozen';
    }

    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }
}
