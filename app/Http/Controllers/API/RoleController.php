<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Role;
use App\Traits\JsonResponseTrait;

class RoleController extends Controller
{
    use JsonResponseTrait;

    public function index()
    {
        return $this->successResponse(Role::all());
    }

    public function store(StoreRoleRequest $request)
    {
        $role = Role::create($request->validated());

        return $this->successResponse($role, 'Role created.', 201);
    }

    public function show(Role $role)
    {
        return $this->successResponse($role);
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {
        $role->update($request->validated());

        return $this->successResponse($role, 'Role updated.');
    }

    public function destroy(Role $role)
    {
        $role->delete();

        return $this->successResponse(null, 'Role deleted.');
    }
}
