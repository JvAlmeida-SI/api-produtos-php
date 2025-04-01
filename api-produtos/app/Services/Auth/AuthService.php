<?php

namespace App\Services\Auth;

use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    protected $userRepository;
    protected $jwtService;

    public function __construct(
        UserRepositoryInterface $userRepository,
        JwtService $jwtService
    ) {
        $this->userRepository = $userRepository;
        $this->jwtService = $jwtService;
    }

    public function register(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        return $this->userRepository->create($data);
    }

    public function login(array $credentials)
    {
        $user = $this->userRepository->findByEmail($credentials['email']);

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return null;
        }

        return $this->jwtService->generateToken($user);
    }

    public function updateProfile(array $data, $userId)
    {
        return $this->userRepository->update($data, $userId);
    }

    public function changePassword(array $data, $userId)
    {
        $user = $this->userRepository->find($userId);

        if (!Hash::check($data['current_password'], $user->password)) {
            return false;
        }

        $this->userRepository->update([
            'password' => Hash::make($data['new_password'])
        ], $userId);

        return true;
    }

    public function deleteAccount($userId)
    {
        return $this->userRepository->delete($userId);
    }
}