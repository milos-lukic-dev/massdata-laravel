<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * Service for users
 *
 * @class User service
 */
class UserService
{
    /**
     * Create user
     *
     * @param array $data
     * @return User
     */
    public function create(array $data): User
    {
        $data['password'] = Hash::make($data['password']);

        return User::create($data);
    }

    /**
     * Update user
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $user = User::findOrFail($id);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        return $user->update($data);
    }

    /**
     * Delete user
     *
     * @param int $id
     * @return string
     */
    public function destroy(int $id): string
    {
        $currentUserId = Auth::id();
        if ($id === $currentUserId) {
            return 'You cannot delete yourself.';
        }

        $user = User::find($id);
        if (!$user) {
            return 'User not found.';
        }

        $isDeleted = $user->delete();

        return $isDeleted ? 'User deleted successfully.' : 'Failed to delete user.';
    }
}
