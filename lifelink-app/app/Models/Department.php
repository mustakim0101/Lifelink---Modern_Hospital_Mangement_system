<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'dept_name',
        'is_active',
    ];

    public function jobApplications(): HasMany
    {
        return $this->hasMany(JobApplication::class, 'applied_department_id');
    }
}
