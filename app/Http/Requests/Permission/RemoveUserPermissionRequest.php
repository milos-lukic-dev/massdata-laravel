<?php

namespace App\Http\Requests\Permission;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request class for removing a permission from a user.
 *
 * @class RemoveUserPermissionRequest
 */
class RemoveUserPermissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('user-management');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'permission_id' => ['required', 'integer', 'exists:permissions,id'],
        ];
    }
}
