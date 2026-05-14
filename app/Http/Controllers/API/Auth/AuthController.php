<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use App\Traits\JsonResponseTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use JsonResponseTrait;

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

    // Ajout d'un compte admin par l'admin
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

    

    public function profile()
    {
        $user = User::where('ref', Auth::user()->ref)->with('roles')->first();
        return $this->successResponse(new UserResource($user));
    }

}
