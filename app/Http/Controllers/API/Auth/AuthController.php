<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\RequestOtpRequest;
use App\Http\Requests\VerifyOtpRequest;
use App\Http\Requests\VerifyEmailRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthenticationService;
use App\Services\OtpService;
use App\Services\TokenService;
use Illuminate\Http\Request;
use App\Traits\JsonResponseTrait;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use JsonResponseTrait;

    public function __construct(
        private AuthenticationService $authenticationService,
        private OtpService $otpService,
        private TokenService $tokenService
    ) {
    }

    /**
     * Login with email and password
     *
     * @group Authentication
     * @unauthenticated
     * @bodyParam email string required The user's email address. Example: user@example.com
     * @bodyParam password string required The user's password. Example: password123
     * @response 200 {"success": true, "data": {"user": {"id": 1, "email": "user@example.com", "name": "John Doe"}, "token": "auth_token_here", "role": "client"}, "message": "User logged in successfully"}
     * @response 401 {"success": false, "message": "Invalid credentials"}
     * @response 404 {"success": false, "message": "Compte deleted"}
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        
        $authResult = $this->authenticationService->authenticateWithPassword(
            $credentials['email'],
            $credentials['password']
        );

        if (!$authResult) {
            return $this->errorResponse('Invalid credentials', 401);
        }

        return $this->successResponse([
            'user' => new UserResource($authResult['user']),
            'token' => $authResult['token'],
            'role' => $authResult['role'],
        ], 'User logged in successfully')->cookie($authResult['cookie']);
    }

    /**
     * Logout current user
     *
     * @group Authentication
     * @authenticated
     * @response 200 {"success": true, "data": "", "message": "Logged out successfully"}
     * @response 401 {"success": false, "message": "Unauthorized"}
     */
    public function logout(Request $request)
    {
        if (!$request->user()) {
            return $this->errorResponse('Unauthorized', 401);
        }

        if (!$request->user()->currentAccessToken()) {
            return $this->errorResponse('No active token found', 401);
        }

        $request->user()->tokens()->delete();

        return $this->successResponse('', 'Logged out successfully')->withCookie(
            $this->tokenService->forgetToken()
        );
    }

    /**
     * Admin login with email and password
     *
     * @group Authentication
     * @unauthenticated
     * @bodyParam email string required Admin email address. Example: admin@example.com
     * @bodyParam password string required Admin password. Example: adminpass123
     * @response 200 {"success": true, "data": {"token": "admin_token_here"}, "message": "Login successful"}
     * @response 401 {"success": false, "message": "Invalid credentials"}
     * @response 403 {"success": false, "message": "Unauthorized"}
     */
    public function loginAdmin(LoginRequest $request)
    {
        $credentials = $request->validated();
        $authResult = $this->authenticationService->authenticateWithPassword(
            $credentials['email'],
            $credentials['password']
        );

        if (!$authResult) {
            return $this->errorResponse('Invalid credentials', 401);
        }

        if ($authResult['role'] !== 'admin') {
            return $this->errorResponse('Unauthorized', 403);
        }

        return $this->successResponse(
            ['token' => $authResult['token']],
            'Login successful'
        )->withCookie($authResult['cookie']);
    }

    /**
     * Request OTP code
     *
     * @group Authentication
     * @unauthenticated
     * @bodyParam email string The user's email address. Example: user@example.com
     * @bodyParam phone_number string The user's phone number. Example: +1234567890
     * @response 200 {"success": true, "data": {"contact": "user@example.com"}, "message": "Code OTP envoyé."}
     * @response 404 {"success": false, "message": "Utilisateur introuvable ou inactif"}
     */
    public function requestOtp(RequestOtpRequest $request)
    {
        $data = $request->validated();
        $user = $this->authenticationService->findActiveUser($data);

        if (!$user) {
            return $this->errorResponse('Utilisateur introuvable ou inactif', 404);
        }

        $channel = isset($data['phone_number']) ? 'sms' : 'mail';
        $this->otpService->sendOtp($user, $channel);

        return $this->successResponse(
            ['contact' => $data['phone_number'] ?? $data['email']],
            'Code OTP envoyé.'
        );
    }

    /**
     * Verify OTP code and login
     *
     * @group Authentication
     * @unauthenticated
     * @bodyParam email string The user's email address. Example: user@example.com
     * @bodyParam phone_number string The user's phone number. Example: +1234567890
     * @bodyParam code string required The OTP code. Example: 123456
     * @response 200 {"success": true, "data": {"user": {"id": 1, "email": "user@example.com", "name": "John Doe"}, "token": "auth_token_here", "role": "client"}, "message": "Connexion par OTP réussie"}
     * @response 422 {"success": false, "message": "Code OTP invalide ou expiré"}
     * @response 404 {"success": false, "message": "Utilisateur introuvable ou inactif"}
     */
    public function verifyOtp(VerifyOtpRequest $request)
    {
        $data = $request->validated();
        $contact = $data['phone_number'] ?? $data['email'];
        $authResult = $this->authenticationService->authenticateWithOtp($contact, $data['code']);

        if (!$authResult) {
            return $this->errorResponse('Code OTP invalide ou expiré', 422);
        }

        return $this->successResponse([
            'user' => new UserResource($authResult['user']),
            'token' => $authResult['token'],
            'role' => $authResult['role'],
        ], 'Connexion par OTP réussie')->cookie($authResult['cookie']);
    }

    /**
     * Create admin account
     *
     * @group Administration
     * @authenticated
     * @bodyParam name string required Admin full name. Example: John Admin
     * @bodyParam first_name string required Admin first name. Example: John
     * @bodyParam last_name string required Admin last name. Example: Admin
     * @bodyParam email string required Admin email. Example: admin@example.com
     * @bodyParam phone_number string required Admin phone. Example: +1234567890
     * @bodyParam password string required Admin password. Example: securepass123
     * @response 201 {"success": true, "data": {"id": 1, "name": "John Admin", "email": "admin@example.com"}, "message": "Admin created successfully"}
     */
    public function createAdmin(RegisterRequest $request)
    {
        $validatedData = $request->validated();
        $admin = $this->authenticationService->createAdmin($validatedData);

        return $this->successResponse(new UserResource($admin), 'Admin created successfully', 201);
    }

    /**
     * Register a new user (requires email verification)
     *
     * @group Authentication
     * @unauthenticated
     * @bodyParam name string required Full name. Example: John Doe
     * @bodyParam first_name string First name. Example: John
     * @bodyParam last_name string Last name. Example: Doe
     * @bodyParam email string required Email address. Example: user@example.com
     * @bodyParam phone_number string Phone number. Example: +1234567890
     * @bodyParam password string required Password. Example: securepass123
     * @response 201 {"success": true, "data": {"id": 1, "name": "John Doe", "email": "user@example.com"}, "message": "Registration successful. Check your email for verification code."}
     * @response 422 {"success": false, "message": "Validation error"}
     */
    public function register(RegisterRequest $request)
    {
        $validatedData = $request->validated();
        $user = $this->authenticationService->createUserWithEmailVerification($validatedData, 'client');

        return $this->successResponse(
            new UserResource($user),
            'Registration successful. Check your email for verification code.',
            201
        );
    }

    /**
     * Verify email during registration
     *
     * @group Authentication
     * @unauthenticated
     * @bodyParam email string required User email. Example: user@example.com
     * @bodyParam code string required OTP verification code. Example: 123456
     * @response 200 {"success": true, "data": {"user": {...}, "message": "Email verified successfully. You can now login."}
     * @response 422 {"success": false, "message": "Code OTP invalide ou expiré"}
     * @response 404 {"success": false, "message": "Utilisateur introuvable"}
     */
    public function verifyEmail(VerifyEmailRequest $request)
    {
        $data = $request->validated();
        $user = $this->authenticationService->verifyEmailAndActivate($data['email'], $data['code']);

        if (!$user) {
            return $this->errorResponse('Code OTP invalide ou expiré', 422);
        }

        return $this->successResponse(
            new UserResource($user),
            'Email verified successfully. You can now login.'
        );
    }

    /**
     * Resend verification email
     *
     * @group Authentication
     * @unauthenticated
     * @bodyParam email string required User email. Example: user@example.com
     * @response 200 {"success": true, "data": {"contact": "user@example.com"}, "message": "Verification code sent to email"}
     * @response 404 {"success": false, "message": "Utilisateur introuvable ou compte déjà activé"}
     */
    public function resendVerificationEmail(RequestOtpRequest $request)
    {
        $data = $request->validated();
        $user = $this->otpService->findUserByContact($data);

        if (!$user) {
            return $this->errorResponse('Utilisateur introuvable ou compte déjà activé', 404);
        }

        if ($user->is_active) {
            return $this->errorResponse('Utilisateur introuvable ou compte déjà activé', 404);
        }

        $this->authenticationService->sendEmailVerificationOtp($user);

        return $this->successResponse(
            ['contact' => $data['email'] ?? $data['phone_number']],
            'Verification code sent to email'
        );
    }

    /**
     * Get current user profile
     *
     * @group Authentication
     * @authenticated
     * @response 200 {"success": true, "data": {"id": 1, "name": "John Doe", "email": "user@example.com", "roles": []}, "message": null}
     * @response 401 {"success": false, "message": "Unauthorized"}
     */
    public function profile()
    {
        $user = Auth::user()->load('roles');

        return $this->successResponse(new UserResource($user));
    }
}
