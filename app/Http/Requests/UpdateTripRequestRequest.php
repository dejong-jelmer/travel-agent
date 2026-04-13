<?php

namespace App\Http\Requests;

use App\Services\Validation\TripRequestValidationRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateTripRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return TripRequestValidationRules::update();
    }
}
