<?php

namespace App\Http\Requests\Import;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;

/**
 * Request class for handling file upload validation for imports.
 *
 * @class UploadFilesRequest
 */
class UploadFilesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        $importType = $this->input('import_type');
        $config = config("imports.{$importType}");

        if (!$config) {
            return false;
        }

        $permission = $config['permission_required'] ?? null;

        return $permission ? $this->user()?->can($permission) : true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $rules = [
            'import_type' => [
                'required',
                'string',
                Rule::in(array_keys(config('imports') ?? [])),
            ],
            'files' => ['required', 'array'],
        ];

        $importType = $this->input('import_type');
        $config = config("imports.{$importType}");

        if ($config && isset($config['files'])) {
            $fileKeys = array_keys($config['files']);

            $rules['files'][] = function ($attribute, $value, $fail) use ($fileKeys) {
                foreach ($fileKeys as $key) {
                    if (isset($value[$key]) && $value[$key] instanceof UploadedFile) {
                        return;
                    }
                }

                $fail('At least one of the required files must be uploaded.');
            };

            foreach ($fileKeys as $fileKey) {
                $rules["files.$fileKey"] = [
                    'file',
                    'mimes:csv,xlsx,xls',
                    'max:10240',
                ];
            }
        }

        return $rules;
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'files.required' => 'At least one file is required.',
        ];
    }
}
