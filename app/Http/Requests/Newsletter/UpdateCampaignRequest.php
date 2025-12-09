<?php

namespace App\Http\Requests\Newsletter;

use App\Enums\Newsletter\CampaignStatus;
use App\Models\NewsletterCampaign;
use App\Services\Validation\Newsletter\CampaignValidationRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateCampaignRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge(
            [
                'id' => ['required', Rule::exists(NewsletterCampaign::class, 'id')],
            ],
            CampaignValidationRules::basic(),
            CampaignValidationRules::heroImageUpdate(),
            CampaignValidationRules::trips(),
        );
    }
}
