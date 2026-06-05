<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\Hash;

class AuthenticationService
{
    public function __construct(
        private OtpService $otpService,
        private TokenService $tokenService
    ) {
    }

    /**
     * Authenticate user with email and password
     *
     * @param string $email
     * @param string $password
     * @return array{user: User, token: string, role: string, cookie: \Illuminate\Http\Cookie}|null
     */
    public function authenticateWithPassword(string $email, string $password): ?array
    {
        $user = User::where('email', $email)->first();
        //dd('user:'. $user);

        if (!$user || !Hash::check($password, (string) $user->password)) {
            return null;
           // dd('Invalid credentials');
        }
        //dd('Authenticated user:'. $user .'password:'. $password);

        return $this->buildAuthenticationResponse($user);
    }

    /**
     * Authenticate user with OTP code
     *
     * @param string $contact Email or phone number
     * @param string $code OTP code
     * @return array{user: User, token: string, role: string, cookie: \Illuminate\Http\Cookie}|null
     */
    public function authenticateWithOtp(string $contact, string $code): ?array
    {
        $user = $this->otpService->verifyOtp($contact, $code);

        if (!$user) {
            return null;
        }

        return $this->buildAuthenticationResponse($user);
    }

    /**
     * Build complete authentication response
     *
     * @param User $user
     * @return array{user: User, token: string, role: string, cookie: \Illuminate\Http\Cookie}|null
     */
    private function buildAuthenticationResponse(User $user): ?array
    {
        if (!$user->is_active) {
            return null;
        }

        $roles = $user->roles()->get();
        // dd('roles:'. $roles);
        if ($roles->isEmpty()) {
            return null;
        }

        $tokenData = $this->tokenService->createToken($user);
        // dd('tokenData:'. $tokenData);

        return [
            'user' => $user,
            'token' => $tokenData['token'],
            'role' => $tokenData['role'],
            'cookie' => $tokenData['cookie'],
        ];
    }

    /**
     * Check if user exists and is active
     *
     * @param array $attributes
     * @return User|null
     */
    public function findActiveUser(array $attributes): ?User
    {
        $user = $this->otpService->findUserByContact($attributes);

        if ($user && $user->is_active) {
            return $user;
        }

        return null;
    }

    /**
     * Create a new user with email verification requirement
     *
     * @param array $data User data (name, email, password, phone_number, etc.)
     * @param string $role Role name (client, seller, admin)
     * @return User
     */
    public function createUserWithEmailVerification(array $data, string $role = 'client'): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'first_name' => $data['first_name'] ?? null,
            'last_name' => $data['last_name'] ?? null,
            'phone_number' => $data['phone_number'] ?? null,
            'is_active' => false, // Not active until email verified
        ]);

        $this->assignRole($user, $role);
        $this->sendEmailVerificationOtp($user);

        return $user;
    }

    /**
     * Verify user email and activate account
     *
     * @param string $email
     * @param string $code OTP code
     * @return User|null
     */
    public function verifyEmailAndActivate(string $email, string $code): ?User
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return null;
        }

        $verifiedUser = $this->otpService->verifyOtp($email, $code);

        if (!$verifiedUser) {
            return null;
        }

        $user->update(['is_active' => true]);

        return $user;
    }

    /**
     * Send OTP for email verification
     *
     * @param User $user
     * @return void
     */
    public function sendEmailVerificationOtp(User $user): void
    {
        $this->otpService->sendOtp($user, 'mail');
    }

    /**
     * Assign a role to a user
     *
     * @param User $user
     * @param string $roleName
     * @return void
     */
    private function assignRole(User $user, string $roleName): void
    {
        $role = Role::where('name', $roleName)->first();

        if ($role) {
            UserRole::create([
                'user_ref' => $user->ref,
                'role_ref' => $role->ref,
                'start_at' => now(),
                'is_active' => true,
            ]);
        }
    }

    /**
     * Create an admin user
     *
     * @param array $data
     * @return User
     */
    public function createAdmin(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'phone_number' => $data['phone_number'],
            'is_active' => true,
        ]);

        $this->assignRole($user, 'admin');

        return $user;
    }
}
