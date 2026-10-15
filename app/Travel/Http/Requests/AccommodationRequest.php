<?php

namespace App\Travel\Http\Requests;

use App\Travel\Enums\AccommodationType;
use App\Travel\Models\City;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * Stores or updates an accommodation. Its arrival route is edited through its legs.
 */
class AccommodationRequest extends FormRequest
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
            'city_id' => ['required', 'integer', Rule::exists(City::class, 'id')],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::enum(AccommodationType::class)],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'check_in' => ['nullable', 'date_format:H:i'],
            'check_out' => ['nullable', 'date_format:H:i'],
            'walk_directions' => ['nullable', 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'city_id' => 'plaats',
            'name' => 'naam',
            'type' => 'soort',
            'address' => 'adres',
            'phone' => 'telefoon',
            'latitude' => 'breedtegraad',
            'longitude' => 'lengtegraad',
            'check_in' => 'inchecken',
            'check_out' => 'uitchecken',
            'walk_directions' => 'looproute',
        ];
    }
}
