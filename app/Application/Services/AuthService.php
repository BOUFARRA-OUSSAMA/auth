<?php

namespace App\Application\Services;

use App\Domain\Interfaces\Repositories\UserRepositoryInterface;
use App\Infrastructure\Persistence\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use InvalidArgumentException;

class AuthService
{
    private UserRepositoryInterface $userRepository;
    private UserService $userService;

    public function __construct(UserRepositoryInterface $userRepository, UserService $userService)
    {
        $this->userRepository = $userRepository;
        $this->userService = $userService;
    }

    /**
     * Register a new user
     *
     * @param string $name
     * @param string $email
     * @param string $password
     * @param string|null $phone
     * @return array
     */
    public function register(string $name, string $email, string $password, ?string $phone = null): array
    {
        // This delegates to the UserService to maintain existing functionality
        return $this->userService->register($name, $email, $password, $phone);
    }

    /**
     * Login a user and generate JWT token
     *
     * @param string $email
     * @param string $password
     * @return array
     */
    public function login(string $email, string $password): array
    {
        // This delegates to the UserService to maintain existing functionality
        return $this->userService->login($email, $password);
    }
}
