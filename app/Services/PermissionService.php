<?php

namespace App\Services;

use App\Models\User;
use Spatie\Permission\Models\Permission;

/**
 * Service for permission
 *
 * @class PermissionService
 */
class PermissionService
{
    protected Permission $permission;
    protected User $user;

    /**
     * PermissionService constructor.
     *
     * @param Permission $permission
     * @param User $user
     */
    public function __construct(Permission $permission, User $user)
    {
        $this->permission = $permission;
        $this->user = $user;
    }

    /**
     * Get permission
     *
     * @param int $permissionId
     * @return mixed
     */
    public function petPermission(int $permissionId)
    {
        return $this->permission->findOrFail($permissionId);
    }

    /**
     * Get paginated permissions, 30 per page.
     *
     * @return mixed
     */
    public function getPermissions()
    {
        return $this->permission->paginate(30);
    }

    /**
     * Create a new permission.
     *
     * @param string $permissionName
     * @return string
     */
    public function create(string $permissionName): string
    {
        $this->permission->create([
            'name' => $permissionName,
        ]);

        return 'Permission created successfully.';
    }

    /**
     * Update a new permission.
     *
     * @param $data
     * @return string
     */
    public function update($data): string
    {
        $permission = $this->permission->find($data['id']);

        if (!$permission) {
            return 'Permission not found.';
        }

        try {
            $permission->update([
                'name' => $data['name'],
            ]);

            return 'Permission updated successfully.';
        } catch (\Exception $ex) {
            return 'Failed to update permission: ' . $ex->getMessage();
        }
    }

    /**
     * Delete a permission by ID and detach it from all users.
     *
     * @param int $permissionId
     * @return string
     */
    public function destroy(int $permissionId): string
    {
        $permission = $this->permission->find($permissionId);

        if (!$permission) {
            return 'Permission not found.';
        }

        try {
            $usersWithPermission = $this->user->permission($permission->name)->get();

            if (!empty($usersWithPermission)) {
                foreach ($usersWithPermission as $user) {
                    $user->revokePermissionTo($permission->name);
                }
            }

            $permission->delete();

            return 'Permission deleted and removed from all users.';
        } catch (\Exception $ex) {
            return 'Failed to delete permission: ' . $ex->getMessage();
        }
    }

    /**
     * Get users that have the given permission
     *
     * @param int $permissionId
     * @return \stdClass
     */
    public function getUsersByPermissionId(int $permissionId): \stdClass
    {
        $permission = $this->permission->findOrFail($permissionId);

        $users = $this->user->permission($permission->name)->paginate(30);

        $data = new \stdClass();
        $data->permission = $permission;
        $data->users = $users;

        return $data;
    }

    /**
     * Assign permission to user with validation.
     *
     * @param $data
     * @return string
     */
    public function assignUserPermission($data): string
    {
        $user = $this->user->find($data['user_id']);
        $permission = $this->permission->find($data['permission_id']);

        if (!$user || !$permission) {
            return 'User or Permission not found.';
        }

        if ($user->hasPermissionTo($permission->name)) {
            return 'User already has this permission.';
        }

        $user->givePermissionTo($permission->name);

        return "Permission '{$permission->name}' assigned to user '{$user->email}' successfully.";
    }

    /**
     * Remove user permission
     *
     * @param array $data
     * @return string
     */
    public function removeUserPermission(array $data): string
    {
        $user = $this->user->find($data['user_id']);
        if (!$user) {
            return 'User not found.';
        }

        $permission = $this->permission->find($data['permission_id']);
        if (!$permission) {
            return 'Permission not found.';
        }

        if (!$user->hasPermissionTo($permission->name)) {
            return 'User does not have this permission.';
        }

        try {
            $user->revokePermissionTo($permission->name);
            return 'Permission successfully removed from user.';
        } catch (\Exception $ex) {
            return 'Failed to remove permission: ' . $ex->getMessage();
        }
    }
}
