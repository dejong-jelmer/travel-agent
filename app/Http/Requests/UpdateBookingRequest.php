<?php

namespace App\Http\Requests;

use App\Enums\Booking\PaymentStatus;
use App\Enums\Booking\Status;
use App\Http\Requests\Traits\ValidatesMainBooker;
use App\Services\Validation\BookingValidationRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateBookingRequest extends FormRequest
{
    use ValidatesMainBooker;

    public function authorize(): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return array_merge(
            [
                'status' => [
                    'required',
                    'string',
                    Rule::enum(Status::class),
                ],
                'payment_status' => [
                    'required',
                    'string',
                    Rule::enum(PaymentStatus::class),
                ],
            ],
            [
                'travelers.*.*.id' => ['required', 'integer', Rule::in($this->booking?->travelers?->modelKeys() ?? [])],
            ],
            BookingValidationRules::contact(),
            BookingValidationRules::travelers(),
            BookingValidationRules::mainBooker(),
        );
    }
}
