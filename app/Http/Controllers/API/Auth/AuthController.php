<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\RequestOtpRequest;
use App\Http\Requests\VerifyOtpRequest;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use App\Services\OtpService;
use Illuminate\Http\Request;
use App\Traits\JsonResponseTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use JsonResponseTrait;

    public function __construct(private OtpService $otpService)
    {
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
        $user = User::where('email', $credentials['email'])
                    ->first();

        if (!$user || !Hash::check($credentials['password'], (string) $user->password)) {
            return $this->errorResponse('Invalid credentials', 401);
        }

        // Check if the user is active
        if (!$user->is_active) {
            return $this->errorResponse('Compte deleted', 404);
        }

        // find the user roles
        $roles = $user->roles()->get();
        if ($roles->isEmpty()) {
            return $this->errorResponse('User has no roles assigned', 403);
        }
        //dd($roles);
        if($user->hasRole('client')) {
            $roleName = 'client';
        } elseif ($user->hasRole('seller')) {
            $roleName = 'seller';
        // } elseif ($user->hasRole('admin')) {
        //     $roleName = 'admin';
        } else {
            return $this->errorResponse('Unauthorized', 403);
        }

        $token = $user->createToken('auth_token', [$roleName])->plainTextToken;
        $cookie = cookie('auth_token', $token, 1440, null, null, false, true);
        
        //$user->notify(new LoginNotification()) ;

        return $this->successResponse([
            'user' => new UserResource($user),
            'token' => $token,
            'role' => $roleName,
        ], 'User logged in successfully')->cookie($cookie);
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
        // Vérifie si l'utilisateur est authentifié
        if (!$request->user()) {
            return $this->errorResponse('Unauthorized', 401);
        }
        // Vérifie si l'utilisateur a un token actif
        if (!$request->user()->currentAccessToken()) {
            return $this->errorResponse('No active token found', 401);
        }
        // supprime tous les tokens de l'utilisateur
        $request->user()->tokens()->delete();
        
        //$request->user()->currentAccessToken()->delete();

        $cookie = Cookie::forget('auth_token');

        return $this->successResponse('', 'Logged out successfully')->withCookie($cookie);
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
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return $this->errorResponse('Invalid credentials', 401);
        }

        $user = Auth::user();

        if (!$user->hasRole('admin')) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        $cookie = cookie('auth_token', $token, 60 * 24); // expire in 1 day

        return $this->successResponse(['token' => $token], 'Login successful')->withCookie($cookie);
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
        $user = $this->otpService->findUserByContact($data);

        if (!$user || !$user->is_active) {
            return $this->errorResponse('Utilisateur introuvable ou inactif', 404);
        }

        $channel = isset($data['phone_number']) ? 'sms' : 'mail';
        $this->otpService->sendOtp($user, $channel);

        return $this->successResponse(['contact' => $data['phone_number'] ?? $data['email']], 'Code OTP envoyé.');
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
        $user = $this->otpService->verifyOtp($contact, $data['code']);

        if (!$user) {
            return $this->errorResponse('Code OTP invalide ou expiré', 422);
        }

        if (!$user->is_active) {
            return $this->errorResponse('Utilisateur introuvable ou inactif', 404);
        }

        $roles = $user->roles()->get();
        if ($roles->isEmpty()) {
            return $this->errorResponse('User has no roles assigned', 403);
        }

        if ($user->hasRole('client')) {
            $roleName = 'client';
        } elseif ($user->hasRole('seller')) {
            $roleName = 'seller';
        } else {
            return $this->errorResponse('Unauthorized', 403);
        }

        $token = $user->createToken('auth_token', [$roleName])->plainTextToken;
        $cookie = cookie('auth_token', $token, 1440, null, null, false, true);

        return $this->successResponse([
            'user' => new UserResource($user),
            'token' => $token,
            'role' => $roleName,
        ], 'Connexion par OTP réussie')->cookie($cookie);
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

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'phone_number' => $validatedData['phone_number'],
            'is_active' => true,
        ]);

        $role = Role::where('name', 'admin')->first();
        UserRole::create([
            'user_ref' => $user->ref,
            'role_ref' => $role->ref,
            'start_at' => now(),
            'is_active' => true,
        ]);

        return $this->successResponse(new UserResource($user), "Admin created successfully", 201);
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
        $user = User::where('ref', Auth::user()->ref)->with('roles')->first();
        return $this->successResponse(new UserResource($user));
    }
}
