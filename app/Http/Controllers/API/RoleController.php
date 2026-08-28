<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use App\Traits\JsonResponseTrait;

/**
 * @group Roles
 *
 * @authenticated
 */
class RoleController extends Controller
{
    use JsonResponseTrait;

    /**
     * List all roles
     *
     * @group Roles
     *
     * @authenticated
     *
     * @response 200 {"success": true, "data": [{"id": 1, "ref": "role_123", "name": "Admin"}], "message": null}
     */
    public function index()
    {
        return $this->successResponse(RoleResource::collection(Role::withCount('users')->get()));
    }

    public function store(StoreRoleRequest $request)
    {
        $role = Role::create($request->validated());

        return $this->successResponse(new RoleResource($role), 'Role created.', 201);
    }

    public function show(Role $role)
    {
        return $this->successResponse(new RoleResource($role));
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {
        $role->update($request->validated());

        return $this->successResponse(new RoleResource($role), 'Role updated.');
    }

    public function destroy(Role $role)
    {
        $role->delete();

        return $this->successResponse(null, 'Role deleted.');
    }
}
