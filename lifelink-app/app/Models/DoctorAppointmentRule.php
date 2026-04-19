<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorAppointmentRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_user_id',
        'department_id',
        'day_of_week',
        'start_time',
        'end_time',
        'daily_capacity',
        'is_active',
    ];

    protected $casts = [
        'day_of_week' => 'integer',
        'daily_capacity' => 'integer',
        'is_active' => 'boolean',
    ];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_user_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}

