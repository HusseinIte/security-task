<?php

namespace App\Services;

use App\Exceptions\CustomException;
use App\Exceptions\NotFoundException;
use App\Models\Permission;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;


/**
 * Class PermissionService
 *
 * This service handles all CRUD operations related to permissions, including soft deletes,
 * restoring deleted permissions, and error handling with detailed logging.
 */
class PermissionService
{
    /**
     * Creates a new permission in the database.
     *
     * @param array $data Data to create the permission, including 'name' and optional 'description'.
     *
     * @return Permission The newly created permission.
     *
     * @throws CustomException If there's a database query error or unexpected error during creation.
     */
    public function createPermission(array $data)
    {
        try {
            $permission = Permission::create([
                'name' => $data['name'],
                'description' => isset($data['description']) ? $data['description'] : null
            ]);
            Log::info("permission created successfully");
            return $permission;
        } catch (QueryException $e) {
            Log::error("Database query error while creating permission: " . $e->getMessage());
            throw new CustomException('Database query error while creating permission', 500);
        } catch (\Exception $e) {
            Log::error('An unexpected error while creating permission: ' . $e->getMessage());
            throw new CustomException('An expected error while creating permission', 500);
        }
    }


    /**
     * Retrieves all permissions from the database.
     *
     * @return \Illuminate\Database\Eloquent\Collection The collection of all permissions.
     *
     * @throws CustomException If there's a database query error or other unexpected error.
     */
    public function getAllPermissions()
    {
        try {
            $permissions = Permission::all();
            return $permissions;
        } catch (QueryException $e) {
            Log::error('Error fetching permissions: ' . $e->getMessage());
            throw new CustomException('Failed to retrieve permissions from the database.', 500);
        } catch (\Exception $e) {
            Log::error('Error fetching permissions: ' . $e->getMessage());
            throw new CustomException($e->getMessage(), 500);
        }
    }
    /**
     * Retrieves a specific permission by its ID.
     *
     * @param int $id The ID of the permission to retrieve.
     *
     * @return Permission The requested permission.
     *
     * @throws NotFoundException If the permission is not found.
     * @throws CustomException If there's a database query error or unexpected error.
     */
    public function showPermission($id)
    {
        try {
            $permission = Permission::findOrFail($id);
            return $permission;
        } catch (ModelNotFoundException $e) {
            Log::error("Permission with id $id not found: " . $e->getMessage());
            throw new NotFoundException('Permission Not Found');
        } catch (QueryException $e) {
            Log::error("Database query error for permission id $id: " . $e->getMessage());
            throw new CustomException('Database query error while retrieving the permission.', 500);
        } catch (\Exception $e) {
            Log::error("An unexepted error occurred while retrieving permission id $id: " . $e->getMessage());
            throw new CustomException('An unexepted error occurred while retriving the permission.', 500);
        }
    }


    /**
     * Updates a specific permission with new data.
     *
     * @param array $data The data to update the permission, with optional keys 'name' and 'description'.
     * @param int $permissionId The ID of the permission to update.
     *
     * @return Permission The updated permission.
     *
     * @throws NotFoundException If the permission is not found.
     * @throws CustomException If there's a database query error or unexpected error during the update.
     */
    public function updatePermission(array $data, $permissionId)
    {
        try {
            $permission = Permission::findOrFail($permissionId);
            $permission->update(array_filter($data));
            Log::info("Permission with id $permissionId successfully updated");
            return $permission;
        } catch (ModelNotFoundException $e) {
            Log::error("Permission with id $permissionId not found for update: " . $e->getMessage());
            throw new NotFoundException('Permission Not Found');
        } catch (QueryException $e) {
            Log::error("Database query error for updating permission id $permissionId: " . $e->getMessage());
            throw new CustomException('Database query error for updating permission', 500);
        } catch (\Exception $e) {
            Log::error("An unexpected error while updating permission id $permissionId: " . $e->getMessage());
            throw new CustomException('An expected error while updating permission', 500);
        }
    }

    /**
     * Soft deletes a specific permission.
     *
     * @param int $permissionId The ID of the permission to delete.
     *
     * @throws NotFoundException If the permission is not found.
     * @throws CustomException If there's a database query error or unexpected error during the deletion.
     */
    public function deletePermission($permissionId)
    {
        try {
            $permission = Permission::findOrFail($permissionId);
            $permission->delete();
            Log::info("Permission with id $permissionId deleted successfully.");
        } catch (ModelNotFoundException $e) {
            Log::error("Permission with id $permissionId not found for deleted: " . $e->getMessage());
            throw new NotFoundException('Permission not found');
        } catch (QueryException $e) {
            Log::error("Database query error while deleting permission id $permissionId: " . $e->getMessage());
            throw new CustomException("Database query error while deleting permission", 500);
        } catch (\Exception $e) {
            Log::error("An unexpected error while deleting permission id $permissionId: " . $e->getMessage());
            throw new CustomException('An expected error while deleting permission.', 500);
        }
    }

    /**
     * Permanently deletes a permission from the database (force delete).
     *
     * @param int $permissionId The ID of the permission to permanently delete.
     *
     * @throws NotFoundException If the permission is not found.
     * @throws CustomException If there's a database query error or unexpected error during the force deletion.
     */
    public function forceDeletePermission($permissionId)
    {
        try {
            $permission = Permission::onlyTrashed()->findOrFail($permissionId);
            $permission->forceDelete();
            Log::info("Permission with id $permissionId deleted permanently.");
        } catch (ModelNotFoundException $e) {
            Log::error("Permission with id $permissionId not found for deleted: " . $e->getMessage());
            throw new CustomException('Permission not found', 404);
        } catch (QueryException $e) {
            Log::error("Database query error while deleting permission id $permissionId: " . $e->getMessage());
            throw new CustomException("Database query error while deleting permission", 500);
        } catch (\Exception $e) {
            Log::error("An unexpected error while deleting permission id $permissionId: " . $e->getMessage());
            throw new CustomException('An expected error while deleting permission.', 500);
        }
    }

       /**
     * Restores a previously soft-deleted permission.
     *
     * @param int $permissionId The ID of the permission to restore.
     *
     * @return Permission The restored permission.
     *
     * @throws NotFoundException If the permission is not found in the soft-deleted records.
     * @throws CustomException If there's a database query error or unexpected error during the restore.
     */
    public function restorePermission($permissionId)
    {
        try {
            $permission = Permission::onlyTrashed()->findOrFail($permissionId);
            $permission->restore();
            Log::info("Permission with id $permissionId restored successfully.");
            return $permission;
        } catch (ModelNotFoundException $e) {
            Log::error("Permission with id $permissionId not found for restore: " . $e->getMessage());
            throw new CustomException('Permission not found', 404);
        } catch (QueryException $e) {
            Log::error("Database query error while restoring permission id $permissionId: " . $e->getMessage());
            throw new CustomException("Database query error while restoring permission", 500);
        } catch (\Exception $e) {
            Log::error("An unexpected error while restoring permission id $permissionId: " . $e->getMessage());
            throw new CustomException('An expected error while restoring permission.', 500);
        }
    }
}
