<?php

namespace App\Http\Requests\Permission;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request class for storing a new permission.
 *
 * @class PermissionStoreRequest
 */
class PermissionStoreRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name'],
        ];
    }
}
