<?php

namespace App\Travel\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * The complete new order of the children of one parent. The Reorder action checks that the ids are exactly the
 * children.
 */
class ReorderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'distinct'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'ids' => 'volgorde',
            'ids.*' => 'onderdeel',
        ];
    }

    /**
     * @return list<int>
     */
    public function ids(): array
    {
        return array_map(fn ($id) => (int) $id, array_values($this->validated('ids')));
    }
}
