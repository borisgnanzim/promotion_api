<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\JsonResponseTrait;

class UserController extends Controller
{
    use JsonResponseTrait;

    /**
     * List all users
     *
     * @group Users
     * @authenticated
     * @response 200 {"success": true, "data": [{"id": 1, "ref": "user_123", "email": "user@example.com", "name": "John Doe"}], "message": null}
     */
    public function index()
    {
        $users = User::with('roles','store')->get();
        return $this->successResponse(UserResource::collection($users));
    }

    /**
     * Create a new user
     *
     * @group Users
     * @authenticated
     * @bodyParam name string required The user's full name. Example: John Doe
     * @bodyParam first_name string required User's first name. Example: John
     * @bodyParam last_name string required User's last name. Example: Doe
     * @bodyParam email string required User's email address. Example: user@example.com
     * @bodyParam password string required User's password. Example: securepass123
     * @bodyParam phone_number string required User's phone number. Example: +212612345678
     * @response 201 {"success": true, "data": {"id": 1, "ref": "user_123", "email": "user@example.com", "name": "John Doe"}, "message": "User created."}
     */
    public function store(StoreUserRequest $request)
    {
        $user = User::create($request->validated());

        return $this->successResponse(new UserResource($user), 'User created.', 201);
    }

    /**
     * Get a specific user
     *
     * @group Users
     * @authenticated
     * @urlParam ref string required The user reference. Example: user_123
     * @response 200 {"success": true, "data": {"id": 1, "ref": "user_123", "email": "user@example.com", "name": "John Doe"}, "message": null}
     */
    public function show(User $user)
    {
        return $this->successResponse(new UserResource($user));
    }

    /**
     * Update a user
     *
     * @group Users
     * @authenticated
     * @urlParam ref string required The user reference. Example: user_123
     * @bodyParam name string User's full name. Example: John Doe Updated
     * @bodyParam phone_number string User's phone number. Example: +212612345678
     * @response 200 {"success": true, "data": {"id": 1, "ref": "user_123", "name": "John Doe Updated"}, "message": "User updated."}
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $user->update($request->validated());

        return $this->successResponse(new UserResource($user), 'User updated.');
    }

    /**
     * Delete a user
     *
     * @group Users
     * @authenticated
     * @urlParam ref string required The user reference. Example: user_123
     * @response 200 {"success": true, "data": null, "message": "User deleted."}
     */
    public function destroy(User $user)
    {
        $user->delete();

        return $this->successResponse(null, 'User deleted.');
    }
}
