<?php

namespace App\Http\Controllers;

use App\Http\Requests\Permission\StorePermissionRequest;
use App\Http\Requests\Permission\UpdatePermissionRequest;
use App\Services\PermissionService;
use App\Traits\ApiResponseTrait;

class PermissionController extends Controller
{
    use ApiResponseTrait;
    protected $permissionService;
    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $permissions = $this->permissionService->getAllPermissions();
        return $this->sendResponse($permissions, 'Permissions retrieve successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePermissionRequest $request)
    {
        $validated = $request->validated();
        $permission = $this->permissionService->createPermission($validated);
        return $this->sendResponse($permission, 'Permission created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show($permissionId)
    {
        $permission = $this->permissionService->showPermission($permissionId);
        return $this->sendResponse($permission, 'Permission retieve successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePermissionRequest $request, $permissionId)
    {
        $validated = $request->validated();
        $permission = $this->permissionService->updatePermission($validated, $permissionId);
        return $this->sendResponse($permission, 'Permission updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($permissionId)
    {
        $this->permissionService->deletePermission($permissionId);
        return $this->sendResponse(null, 'Permission deleted successfully.');
    }

    public function forceDelete($permissionId)
    {
        $this->permissionService->forceDeletePermission($permissionId);
        return $this->sendResponse(null, 'Permission deleted permanently.');
    }

    public function restore($permissionId)
    {
        $this->permissionService->restorePermission($permissionId);
        return $this->sendResponse(null, 'Permission restored successfully.');
    }
}
