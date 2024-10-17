<?php

namespace App\Http\Controllers;

use App\Http\Requests\Role\AssignRolePermissionsRequest;
use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Models\Role;
use App\Services\RoleService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    use ApiResponseTrait;
    protected $roleService;
    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = $this->roleService->getAllRoles();
        return $this->sendResponse($roles, 'Roles retrieve successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request)
    {
        $validated = $request->validated();
        $role = $this->roleService->createRole($validated);
        return $this->sendResponse($role, 'Role created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show($roleId)
    {
        $role = $this->roleService->showRole($roleId);
        return $this->sendResponse($role, 'role retieve successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, $roleId)
    {
        $validated = $request->validated();
        $role = $this->roleService->updateRole($validated, $roleId);
        return $this->sendResponse($role, 'Role updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($roleId)
    {
        $this->roleService->deleteRole($roleId);
        return $this->sendResponse(null, 'role deleted successfully.');
    }

    public function forceDelete($roleId)
    {
        $this->roleService->forceDeleteRole($roleId);
        return $this->sendResponse(null, 'role deleted permanently.');
    }

    public function restore($roleId)
    {
        $role = $this->roleService->restoreRole($roleId);
        return $this->sendResponse($role, 'role restored successfully.');
    }

    public function assignRolePermissions(AssignRolePermissionsRequest $request, $roleId)
    {
        $validated = $request->validated();
        $this->roleService->assignRolePermessions($validated, $roleId);
        return $this->sendResponse(null, 'Permissions have been assigned to the role successfully.');
    }
}
