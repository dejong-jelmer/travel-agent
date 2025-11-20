<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateNewsletterSubscriptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $emailValidation = config('contact.validation-rules.email');

        return [
            'name' => ['nullable', 'max:100'],
            'email' => [
                'required',
                $emailValidation,
                'max:100',
                Rule::unique('newsletter_subscribers', 'email')
                    ->whereNull('unsubscribed_at'),
            ],
        ];
    }
}
