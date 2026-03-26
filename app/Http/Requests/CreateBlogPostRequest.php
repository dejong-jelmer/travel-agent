<?php

namespace App\Http\Requests;

use App\Services\Validation\BlogPostValidationRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreateBlogPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return BlogPostValidationRules::basic();
    }
}
