<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DataTableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'sort' => ['nullable', 'string', 'max:255'],
            'direction' => ['nullable', 'string', 'in:asc,desc'],
        ];
    }

    /**
     * Get validated filter values for a given set of filterable fields
     *
     * @param  array  $filterableFields  Array of field names that can be filtered
     * @return array Validated filter values
     */
    public function getValidatedFilters(array $filterableFields = []): array
    {
        $rules = [];
        foreach ($filterableFields as $field) {
            $rules[$field] = ['nullable', 'string', 'max:255'];
        }

        $validator = Validator::make($this->all(), $rules);

        return array_filter($validator->validated());
    }
}
