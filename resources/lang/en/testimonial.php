<?php

return [
    // Page titles
    'title_index' => 'Testimonials',
    'title_create' => 'New testimonial',
    'title_edit' => 'Edit testimonial',

    // Flash messages
    'created' => 'Testimonial has been created',
    'updated' => 'Testimonial has been updated',
    'deleted' => 'Testimonial has been deleted',
    'published' => 'Testimonial has been published',
    'unpublished' => 'Testimonial has been taken offline',

    // Relationships
    'relationship' => [
        'customer' => 'Customer',
        'personal' => 'Personal (family, friend)',
    ],

    'validation' => [
        'quote_required' => 'Enter a quote.',
        'quote_max' => 'The quote can contain at most :max characters.',
        'body_max' => 'The full account can contain at most :max characters.',
        'author_name_required' => 'Enter the name of the author.',
        'relationship_required' => 'Choose the relationship of the author.',
        'relationship_enum' => 'Choose a valid relationship.',
        'author_context_required' => 'Enter the context for a personal story, so it is clear it is not a customer review.',
        'author_context_max' => 'The context can contain at most :max characters.',
        'link_url_url' => 'Enter a valid link starting with http:// or https://.',
    ],
];
