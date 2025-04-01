<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\Auth\AuthService;
use App\Services\Auth\JwtService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $userRepository;
    protected $jwtService;
    protected $authService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = Mockery::mock(UserRepositoryInterface::class);
        $this->jwtService = Mockery::mock(JwtService::class);
        $this->authService = new AuthService($this->userRepository, $this->jwtService);
    }

    public function test_user_registration()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $this->userRepository->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($data) {
                return Hash::check('password', $data['password']);
            }))
            ->andReturn(new User($userData));

        $user = $this->authService->register($userData);

        $this->assertEquals('Test User', $user->name);
    }

    public function test_successful_login()
    {
        $user = User::factory()->create(['password' => Hash::make('password')]);
        $token = 'test_token';

        $this->userRepository->shouldReceive('findByEmail')
            ->once()
            ->with($user->email)
            ->andReturn($user);

        $this->jwtService->shouldReceive('generateToken')
            ->once()
            ->with($user)
            ->andReturn($token);

        $result = $this->authService->login([
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertEquals($token, $result);
    }

    public function test_failed_login()
    {
        $this->userRepository->shouldReceive('findByEmail')
            ->once()
            ->with('wrong@example.com')
            ->andReturn(null);

        $result = $this->authService->login([
            'email' => 'wrong@example.com',
            'password' => 'password',
        ]);

        $this->assertNull($result);
    }
}