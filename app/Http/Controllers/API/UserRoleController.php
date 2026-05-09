<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRoleRequest;
use App\Http\Requests\UpdateUserRoleRequest;
use App\Models\UserRole;
use App\Traits\JsonResponseTrait;

class UserRoleController extends Controller
{
    use JsonResponseTrait;

    public function index()
    {
        return $this->successResponse(UserRole::all());
    }

    public function store(StoreUserRoleRequest $request)
    {
        $userRole = UserRole::create($request->validated());

        return $this->successResponse($userRole, 'User role created.', 201);
    }

    public function show(UserRole $userRole)
    {
        return $this->successResponse($userRole);
    }

    public function update(UpdateUserRoleRequest $request, UserRole $userRole)
    {
        $userRole->update($request->validated());

        return $this->successResponse($userRole, 'User role updated.');
    }

    public function destroy(UserRole $userRole)
    {
        $userRole->delete();

        return $this->successResponse(null, 'User role deleted.');
    }
}
