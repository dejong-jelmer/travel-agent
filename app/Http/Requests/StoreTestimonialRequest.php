<?php

namespace App\Http\Requests;

use App\Enums\TestimonialRelationship;
use App\Models\Testimonial;
use App\Services\Validation\ImageValidationRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreTestimonialRequest extends FormRequest
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
     * The author context is required for a personal testimonial, so a story by family or a friend always says so.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'trip_id' => ['nullable', 'integer', 'exists:trips,id'],
            'quote' => ['required', 'string', 'max:'.Testimonial::MAX_QUOTE_LENGTH],
            'body' => ['nullable', 'string', 'max:'.Testimonial::MAX_BODY_LENGTH],
            'author_name' => ['required', 'string', 'max:255'],
            'relationship' => ['required', Rule::enum(TestimonialRelationship::class)],
            'author_context' => [
                Rule::requiredIf($this->input('relationship') === TestimonialRelationship::Personal->value),
                'nullable',
                'string',
                'max:'.Testimonial::MAX_AUTHOR_CONTEXT_LENGTH,
            ],
            'travelled_on' => ['nullable', 'date'],
            'photo' => ['nullable', ...ImageValidationRules::baseImageOrString()],
            // Only web links: the URL ends up in an href
            'link_url' => ['nullable', 'url:http,https', 'max:255'],
            'link_label' => ['nullable', 'string', 'max:255'],
            'is_published' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'quote.required' => __('testimonial.validation.quote_required'),
            'quote.max' => __('testimonial.validation.quote_max'),
            'body.max' => __('testimonial.validation.body_max'),
            'author_name.required' => __('testimonial.validation.author_name_required'),
            'relationship.required' => __('testimonial.validation.relationship_required'),
            'relationship.enum' => __('testimonial.validation.relationship_enum'),
            'author_context.required' => __('testimonial.validation.author_context_required'),
            'author_context.max' => __('testimonial.validation.author_context_max'),
            'link_url.url' => __('testimonial.validation.link_url_url'),
        ];
    }
}
