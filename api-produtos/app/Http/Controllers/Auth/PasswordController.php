<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;

class PasswordController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $success = $this->authService->changePassword(
            $request->validated(), 
            auth()->id()
        );

        if (!$success) {
            return response()->json(['error' => 'Current password is incorrect'], 400);
        }

        return response()->json(['message' => 'Password changed successfully']);
    }
}