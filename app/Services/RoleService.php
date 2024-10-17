<?php

namespace App\Services;

use App\Exceptions\CustomException;
use App\Exceptions\NotFoundException;
use App\Models\Role;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use PHPOpenSourceSaver\JWTAuth\Claims\Custom;
use PhpParser\Node\Expr\FuncCall;

class RoleService
{
     /**
     * Create a new role.
     *
     * @param array $data The data for creating the role.
     * @return Role The created role.
     * @throws CustomException if there's a database error or an unexpected error occurs.
     */
    public function createRole(array $data)
    {
        try {
            $role = Role::create([
                'name' => $data['name'],
                'description' => isset($data['description']) ? $data['description'] : null
            ]);
            Log::info("role created successfully");
            return $role;
        } catch (QueryException $e) {
            Log::error("Database query error while creating role: " . $e->getMessage());
            throw new CustomException('Database query error while creating role', 500);
        } catch (\Exception $e) {
            Log::error('An unexpected error while creating role: ' . $e->getMessage());
            throw new CustomException('An expected error while creating role', 500);
        }
    }

     /**
     * Retrieve all roles.
     *
     * @return \Illuminate\Database\Eloquent\Collection|Role[] The list of all roles.
     * @throws CustomException if there's a database error or an unexpected error occurs.
     */
    public function getAllRoles()
    {
        try {
            $roles = Role::all();
            return $roles;
        } catch (QueryException $e) {
            Log::error('Error fetching roles: ' . $e->getMessage());
            throw new CustomException('Failed to retrieve roles from the database.', 500);
        } catch (\Exception $e) {
            Log::error('Error fetching roles: ' . $e->getMessage());
            throw new CustomException($e->getMessage(), 500);
        }
    }

    /**
     * Retrieve a role by its ID.
     *
     * @param int $id The ID of the role.
     * @return Role The retrieved role.
     * @throws NotFoundException if the role is not found.
     * @throws CustomException if there's a database error or an unexpected error occurs.
     */
    public function showRole($id)
    {
        try {
            $role = Role::findOrFail($id);
            Log::info("Role id $id retrieved successfully");
            return $role;
        } catch (ModelNotFoundException $e) {
            Log::error("Role with id $id not found: " . $e->getMessage());
            throw new NotFoundException('Role Not Found');
        } catch (QueryException $e) {
            Log::error("Database query error for role id $id: " . $e->getMessage());
            throw new CustomException('Database query error while retrieving the role.', 500);
        } catch (\Exception $e) {
            Log::error("An unexepted error occurred while retrieving role id $id: " . $e->getMessage());
            throw new CustomException('An unexepted error occurred while retriving the role.', 500);
        }
    }

      /**
     * Update a role by its ID.
     *
     * @param array $data The data to update the role.
     * @param int $roleId The ID of the role.
     * @return Role The updated role.
     * @throws NotFoundException if the role is not found.
     * @throws CustomException if there's a database error or an unexpected error occurs.
     */
    public function updateRole(array $data, $roleId)
    {
        try {
            $role = Role::findOrFail($roleId);
            $role->update(array_filter($data));
            Log::info("Role with id $roleId successfully updated");
            return $role;
        } catch (ModelNotFoundException $e) {
            Log::error("Role with id $roleId not found for update: " . $e->getMessage());
            throw new NotFoundException('Role Not Found');
        } catch (QueryException $e) {
            Log::error("Database query error for updating role id $roleId: " . $e->getMessage());
            throw new CustomException('Database query error for updating role', 500);
        } catch (\Exception $e) {
            Log::error("An unexpected error while updating role id $roleId: " . $e->getMessage());
            throw new CustomException('An expected error while updating role', 500);
        }
    }

      /**
     * Delete a role by its ID.
     *
     * @param int $roleId The ID of the role to delete.
     * @throws NotFoundException if the role is not found.
     * @throws CustomException if there's a database error or an unexpected error occurs.
     */
    public function deleteRole($roleId)
    {
        try {
            $role = Role::findOrFail($roleId);
            $role->delete();
            Log::info("Role with id $roleId deleted successfully.");
        } catch (ModelNotFoundException $e) {
            Log::error("Role with id $roleId not found for deleted: " . $e->getMessage());
            throw new NotFoundException('Role not found');
        } catch (QueryException $e) {
            Log::error("Database query error while deleting role id $roleId: " . $e->getMessage());
            throw new CustomException("Database query error while deleting role", 500);
        } catch (\Exception $e) {
            Log::error("An unexpected error while deleting role id $roleId: " . $e->getMessage());
            throw new CustomException('An expected error while deleting role.', 500);
        }
    }

      /**
     * Permanently delete a soft-deleted role.
     *
     * @param int $roleId The ID of the role to force delete.
     * @throws NotFoundException if the role is not found in the trashed records.
     * @throws CustomException if there's a database error or an unexpected error occurs.
     */
    public function forceDeleteRole($roleId)
    {
        try {
            $role = Role::onlyTrashed()->findOrFail($roleId);
            $role->forceDelete();
            Log::info("Role with id $roleId deleted permanently.");
        } catch (ModelNotFoundException $e) {
            Log::error("Role with id $roleId not found for force delete: " . $e->getMessage());
            throw new NotFoundException('Role not found');
        } catch (QueryException $e) {
            Log::error("Database query error while force deleting role id $roleId: " . $e->getMessage());
            throw new CustomException("Database query error while force deleting role", 500);
        } catch (\Exception $e) {
            Log::error("An unexpected error while force deleting role id $roleId: " . $e->getMessage());
            throw new CustomException('An expected error while force deleting role.', 500);
        }
    }

     /**
     * Restore a soft-deleted role.
     *
     * @param int $roleId The ID of the role to restore.
     * @return Role The restored role.
     * @throws NotFoundException if the role is not found in the trashed records.
     * @throws CustomException if there's a database error or an unexpected error occurs.
     */
    public function restoreRole($roleId)
    {
        try {
            $role = Role::onlyTrashed()->findOrFail($roleId);
            $role->restore();
            Log::info("Role with id $roleId restored successfully.");
            return $role;
        } catch (ModelNotFoundException $e) {
            Log::error("Role with id $roleId not found for restore: " . $e->getMessage());
            throw new NotFoundException('Role not found');
        } catch (QueryException $e) {
            Log::error("Database query error while restore role id $roleId: " . $e->getMessage());
            throw new CustomException("Database query error while restore role", 500);
        } catch (\Exception $e) {
            Log::error("An unexpected error while restore role id $roleId: " . $e->getMessage());
            throw new CustomException('An expected error while restore role.', 500);
        }
    }

     /**
     * Assign permissions to a role.
     *
     * @param array $data The permission data containing permission_ids.
     * @param int $roleId The ID of the role to assign permissions.
     * @throws NotFoundException if the role is not found.
     * @throws CustomException if an unexpected error occurs while assigning permissions.
     */
    public function assignRolePermessions(array $data, $roleId)
    {
        try {
            $role = Role::findOrFail($roleId);
            $role->permissions()->attach($data['permission_ids']);
            Log::info("Permissions have been assigned to the role id $roleId successfully.");
        } catch (ModelNotFoundException $e) {
            Log::error("Role with id $roleId not found to assigne permissions: " . $e->getMessage());
            throw new NotFoundException('Role not found');
        } catch (\Exception $e) {
            Log::error("An unexpected error while assign permission ids to the role id $roleId: " . $e->getMessage());
            throw new CustomException('An expected error while assign permissions to role.', 500);
        }
    }
}
