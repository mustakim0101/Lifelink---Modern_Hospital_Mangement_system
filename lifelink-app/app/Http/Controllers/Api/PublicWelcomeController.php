<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DonorProfile;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;

class PublicWelcomeController extends Controller
{
    public function metrics(): JsonResponse
    {
        $metrics = Cache::remember('public.welcome.metrics', now()->addSeconds(60), function () {
            $recentWeekWindow = Carbon::now()->startOfDay()->subDays(7)->toDateString();

            $patientsCount = Patient::query()
                ->where('is_active', true)
                ->count();

            $medicalStaffCount = User::query()
                ->where('account_status', 'Active')
                ->whereHas('roles', static function ($query) {
                    $query->whereIn('role_name', ['Doctor', 'Nurse']);
                })
                ->distinct('users.id')
                ->count('users.id');

            $activeDonorsCount = DonorProfile::query()
                ->where('is_eligible', true)
                ->whereHas('availabilities', static function ($query) use ($recentWeekWindow) {
                    $query->where('is_available', true)
                        ->whereDate('week_start_date', '>=', $recentWeekWindow);
                })
                ->count();

            return [
                'patients' => (int) $patientsCount,
                'medical_staff' => (int) $medicalStaffCount,
                'active_donors' => (int) $activeDonorsCount,
            ];
        });

        return response()->json([
            'metrics' => $metrics,
            'generated_at' => now()->toISOString(),
        ]);
    }
}
