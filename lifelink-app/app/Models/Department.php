<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'dept_name',
        'slug',
        'short_description',
        'banner_title',
        'banner_description',
        'organ_coverage_json',
        'services_json',
        'sort_order',
        'icon_key',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'organ_coverage_json' => 'array',
        'services_json' => 'array',
        'sort_order' => 'integer',
    ];

    public function jobApplications(): HasMany
    {
        return $this->hasMany(JobApplication::class, 'applied_department_id');
    }

    public function careUnits(): HasMany
    {
        return $this->hasMany(CareUnit::class);
    }

    public function admissions(): HasMany
    {
        return $this->hasMany(Admission::class);
    }

    public function departmentAdmins(): HasMany
    {
        return $this->hasMany(DepartmentAdmin::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function doctors(): HasMany
    {
        return $this->hasMany(Doctor::class);
    }

    public function doctorAppointmentRules(): HasMany
    {
        return $this->hasMany(DoctorAppointmentRule::class);
    }

    public function nurses(): HasMany
    {
        return $this->hasMany(Nurse::class);
    }

    public function bloodRequests(): HasMany
    {
        return $this->hasMany(BloodRequest::class);
    }

    public function doctorReviews(): HasMany
    {
        return $this->hasMany(DoctorReview::class);
    }

    public function scopePublicCatalog(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->whereNotNull('slug')
            ->orderByRaw('CASE WHEN sort_order IS NULL THEN 1 ELSE 0 END')
            ->orderBy('sort_order')
            ->orderBy('dept_name');
    }

    public function getOrganCoverageAttribute(): array
    {
        return is_array($this->organ_coverage_json) ? $this->organ_coverage_json : [];
    }

    public function getServicesAttribute(): array
    {
        return is_array($this->services_json) ? $this->services_json : [];
    }
}
