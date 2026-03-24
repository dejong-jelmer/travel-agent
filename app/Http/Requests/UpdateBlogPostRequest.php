<?php

namespace App\Http\Requests;

use App\Services\Validation\BlogPostValidationRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateBlogPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return BlogPostValidationRules::basicUpdate([
            'slug' => ['required', 'string', 'max:255', Rule::unique('blog_posts', 'slug')->ignore($this->route('post')->id)],
        ]);
    }
}
