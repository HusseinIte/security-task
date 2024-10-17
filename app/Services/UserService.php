<?php

namespace App\Services;

use App\Exceptions\CustomException;
use App\Exceptions\NotFoundException;
use App\Http\Requests\StoreUserRequest;
use App\Models\Book;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

/**
 * Class UserService
 * @package App\Services
 *
 * Service class for managing users in the system.
 */
class UserService
{
    /**
     * Creates a new user in the system.
     *
     * @param array $data User details for creation.
     * @return \App\Models\User Created user instance.
     * @throws CustomException If a database or other error occurs.
     */
    public function createUser(array $data)
    {
        try {
            return User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => $data['password'],
            ]);
        } catch (QueryException $e) {
            Log::error('Error while creating user: ' . $e->getMessage());
            throw new CustomException('Failed to create user due to a database error', 500);
        } catch (Exception $e) {
            Log::error('An unexpected error while creating user: ' . $e->getMessage());
            throw new CustomException('An unexpected error while creating User: ' . $e->getMessage());
        }
    }

    /**
     * Retrieves all users in the system.
     *
     * @return \Illuminate\Database\Eloquent\Collection Collection of users.
     * @throws CustomException If a database or other error occurs.
     */
    public function getAllUsers()
    {
        try {
            $users = User::all();
            return $users;
        } catch (QueryException $e) {
            Log::error('Error fetching users: ' . $e->getMessage());
            throw new CustomException('Failed to retrieve users from the database.', 500);
        } catch (Exception $e) {
            Log::error('Error fetching users: ' . $e->getMessage());
            throw new CustomException('An unexpected error while fetching users', 500);
        }
    }

    /**
     * Retrieves a specific user by ID.
     *
     * @param int $id The ID of the user to retrieve.
     * @return \App\Models\User The retrieved user.
     * @throws NotFoundException If the user is not found.
     * @throws CustomException If a database or other error occurs.
     */
    public function showUser($id)
    {
        try {
            $user = User::findOrFail($id);
            return $user;
        } catch (ModelNotFoundException $e) {
            Log::error("User with id $id not found: " . $e->getMessage());
            throw new NotFoundException('User Not Found');
        } catch (QueryException $e) {
            Log::error("Database query error for user id $id: " . $e->getMessage());
            throw new CustomException('Database query error while retrieving the user.', 500);
        } catch (Exception $e) {
            Log::error("An unexepted error occurred while retrieving user id $id: " . $e->getMessage());
            throw new CustomException('An unexepted error occurred while retriving the user.', 500);
        }
    }


    /**
     * Updates an existing user by ID.
     *
     * @param array $data Data to update the user.
     * @param int $userId The ID of the user to update.
     * @return \App\Models\User The updated user.
     * @throws NotFoundException If the user is not found.
     * @throws CustomException If a database or other error occurs.
     */
    public function updateUser(array $data, $userId)
    {
        try {
            $user = User::findOrFail($userId);
            $user->update(array_filter($data));
            Log::info("User with id $userId successfully updated");
            return $user;
        } catch (ModelNotFoundException $e) {
            Log::error("User with id $userId not found for update: " . $e->getMessage());
            throw new NotFoundException('User Not Found');
        } catch (QueryException $e) {
            Log::error("Database query error for updating user id $userId: " . $e->getMessage());
            throw new CustomException('Database query error for updating user', 500);
        } catch (Exception $e) {
            Log::error("An unexpected error while updating user id $userId: " . $e->getMessage());
            throw new CustomException('An expected error while updating user', 500);
        }
    }


    /**
     * Deletes a user by ID.
     *
     * @param int $userId The ID of the user to delete.
     * @return void
     * @throws NotFoundException If the user is not found.
     * @throws CustomException If a database or other error occurs.
     */
    public function deleteUser($userId)
    {
        try {
            $user = User::findOrFail($userId);
            $user->delete();
            Log::info("User with id $userId deleted successfully.");
        } catch (ModelNotFoundException $e) {
            Log::error("User with id $userId not found for deleted: " . $e->getMessage());
            throw new NotFoundException('User not found');
        } catch (QueryException $e) {
            Log::error("Database query error while deleting user id $userId: " . $e->getMessage());
            throw new CustomException("Database query error while deleting user", 500);
        } catch (Exception $e) {
            Log::error("An unexpected error while deleting user id $userId: " . $e->getMessage());
            throw new CustomException('An expected error while deleting user.', 500);
        }
    }

    /**
     * Force deletes a user permanently by ID (even if soft-deleted).
     *
     * @param int $userId The ID of the user to force delete.
     * @return void
     * @throws NotFoundException If the user is not found.
     * @throws CustomException If a database or other error occurs.
     */
    public function forceDeleteUser($userId)
    {
        try {
            $user = User::onlyTrashed()->findOrFail($userId);
            $user->forceDelete();
            Log::info("User with id $userId deleted permanently.");
        } catch (ModelNotFoundException $e) {
            Log::error("User with id $userId not found for force deleted: " . $e->getMessage());
            throw new NotFoundException('User not found');
        } catch (QueryException $e) {
            Log::error("Database query error while force deleting user id $userId: " . $e->getMessage());
            throw new CustomException("Database query error while force deleting user", 500);
        } catch (Exception $e) {
            Log::error("An unexpected error while force deleting user id $userId: " . $e->getMessage());
            throw new CustomException('An expected error while force deleting user.', 500);
        }
    }

    /**
     * Restores a soft-deleted user by ID.
     *
     * @param int $userId The ID of the user to restore.
     * @return \App\Models\User The restored user.
     * @throws NotFoundException If the user is not found.
     * @throws CustomException If a database or other error occurs.
     */
    public function restoreUser($userId)
    {
        try {
            $user = User::onlyTrashed()->findOrFail($userId);
            $user->restore();
            Log::info("User with id $userId restored successfully.");
            return $user;
        } catch (ModelNotFoundException $e) {
            Log::error("User with id $userId not found for restore: " . $e->getMessage());
            throw new CustomException('User not found', 404);
        } catch (QueryException $e) {
            Log::error("Database query error while restoring user id $userId: " . $e->getMessage());
            throw new CustomException("Database query error while restoring user", 500);
        } catch (Exception $e) {
            Log::error("An unexpected error while restoring user id $userId: " . $e->getMessage());
            throw new CustomException('An expected error while restoring user.', 500);
        }
    }

    /**
     * Assigns roles to a user.
     *
     * @param array $data The array containing role IDs to assign.
     * @param int $userId The ID of the user to assign roles.
     * @return void
     * @throws NotFoundException If the user is not found.
     * @throws CustomException If a database or other error occurs.
     */
    public function assignUserRoles(array $data, $userId)
    {
        try {
            $user = User::findOrFail($userId);
            $user->roles()->attach($data['role_ids']);
            Log::info("Roles have been assigned to the user id $userId successfully.");
        } catch (ModelNotFoundException $e) {
            Log::error("User with id $userId not found to assigne roles: " . $e->getMessage());
            throw new CustomException('User not found', 404);
        } catch (Exception $e) {
            Log::error("An unexpected error while assign role ids to user id $userId: " . $e->getMessage());
            throw new CustomException('An expected error while assign roles to user.', 500);
        }
    }
}
