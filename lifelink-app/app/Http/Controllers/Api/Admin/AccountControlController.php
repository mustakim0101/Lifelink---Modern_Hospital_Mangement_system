<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccountControlController extends Controller
{
    public function freeze(Request $request, User $user): JsonResponse
    {
        $admin = auth('api')->user();

        if ($admin->id === $user->id) {
            return response()->json([
                'message' => 'Admin cannot freeze own account.',
            ], 422);
        }

        $user->update([
            'account_status' => 'Frozen',
            'frozen_at' => now(),
            'frozen_by_user_id' => $admin->id,
        ]);

        return response()->json([
            'message' => 'User account frozen',
            'user' => $this->statusPayload($user->fresh()),
        ]);
    }

    public function unfreeze(User $user): JsonResponse
    {
        $user->update([
            'account_status' => 'Active',
            'frozen_at' => null,
            'frozen_by_user_id' => null,
        ]);

        return response()->json([
            'message' => 'User account unfrozen',
            'user' => $this->statusPayload($user->fresh()),
        ]);
    }

    public function status(User $user): JsonResponse
    {
        return response()->json([
            'user' => $this->statusPayload($user),
        ]);
    }

    private function statusPayload(User $user): array
    {
        return [
            'id' => $user->id,
            'email' => $user->email,
            'fullName' => $user->full_name ?? $user->name,
            'account_status' => $user->account_status,
            'frozen_at' => optional($user->frozen_at)->toISOString(),
            'frozen_by_user_id' => $user->frozen_by_user_id,
            'roles' => $user->roles()->pluck('role_name')->values(),
        ];
    }
}
