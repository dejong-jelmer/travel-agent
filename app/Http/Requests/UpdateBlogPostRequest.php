<?php

namespace App\Http\Requests;

use App\Services\Validation\BlogPostValidationRules;
use App\Services\Validation\ImageValidationRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateBlogPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return BlogPostValidationRules::basic([
            'featured_image' => array_merge(['nullable'], ImageValidationRules::baseImageOrString()),
        ]);
    }
}
