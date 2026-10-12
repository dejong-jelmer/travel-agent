<?php

namespace App\Travel\Http\Requests;

use App\Travel\Models\Accommodation;
use App\Travel\Models\Stay;
use App\Travel\Validation\StayInvariants;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Stores or updates a stay. Its position is set on create and changed by reordering, never here.
 */
class StayRequest extends FormRequest
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
            'accommodation_id' => ['required', 'integer', Rule::exists(Accommodation::class, 'id')],
            'arrival' => ['nullable', 'date'],
            'departure' => ['nullable', 'date'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'accommodation_id' => 'accommodatie',
            'arrival' => 'aankomstdatum',
            'departure' => 'vertrekdatum',
        ];
    }

    /**
     * Check the stay as it would be saved against its invariants, once its fields are valid.
     *
     * @return list<callable(Validator): void>
     */
    public function after(): array
    {
        return [function (Validator $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $stay = $this->route('stay');
            $stay = $stay instanceof Stay ? clone $stay : new Stay;

            foreach (StayInvariants::check($stay->fill($validator->validated())) as $invariant) {
                $validator->errors()->add($invariant->field(), $invariant->message());
            }
        }];
    }
}
