<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Nurse extends Model
{
    use HasFactory;

    protected $primaryKey = 'nurse_id';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'nurse_id',
        'department_id',
        'ward_assignment_note',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nurse_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function vitalSignLogs(): HasMany
    {
        return $this->hasMany(NurseVitalSignLog::class, 'nurse_id', 'nurse_id');
    }
}
