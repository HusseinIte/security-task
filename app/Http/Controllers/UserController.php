<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\AssignUserRolesRequest;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Services\UserService;
use App\Traits\ApiResponseTrait;

class UserController extends Controller
{
    use ApiResponseTrait;
    protected $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $users = $this->userService->getAllUsers();
        return $this->sendResponse($users, 'Users retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();
        $user = $this->userService->createUser($validated);
        return $this->sendResponse($user, 'User created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $user = $this->userService->showUser($id);
        return $this->sendResponse($user, 'user has been retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, string $id)
    {
        $validated = $request->validated();
        $user = $this->userService->updateUser($validated, $id);
        return $this->sendResponse($user, 'user updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $this->userService->deleteUser($id);
        return $this->sendResponse(null, 'user deleted successfully.');
    }

    public function forceDelete(int $id)
    {
        $this->userService->forceDeleteUser($id);
        return $this->sendResponse(null, 'user deleted permanently.');
    }

    public function restore(int $id)
    {
        $user = $this->userService->restoreUser($id);
        return $this->sendResponse($user, 'user restored successfully.');
    }
    public function assignUserRoles(AssignUserRolesRequest $request, int $userId)
    {
        $validated = $request->validated();
        $this->userService->assignUserRoles($validated, $userId);
        return $this->sendResponse(null, 'Roles have been assigned to the user successfully.');
    }
}
