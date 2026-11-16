<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'booking_season_end' => ['nullable', 'date', 'after_or_equal:today'],
            'booking_fee' => ['nullable', 'numeric', 'min:0'],
            'guarantee_fund' => ['nullable', 'numeric', 'min:0'],
            'emergency_fund' => ['nullable', 'numeric', 'min:0'],
            'default_booking_margin_percentage' => ['nullable', 'numeric', 'between:0,95'],
            'default_booking_margin_amount' => ['nullable', 'numeric', 'min:0'],
            'default_booking_fee_per_person' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
