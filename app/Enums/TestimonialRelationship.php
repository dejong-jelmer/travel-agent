<?php

namespace App\Enums;

use App\Enums\Traits\HasTranslatableLabel;
use App\Enums\Traits\Selectable;

/**
 * How the author of a testimonial relates to the organisation: a paying customer, or someone close to it (family,
 * a friend) whose story must never be presented as a customer review.
 */
enum TestimonialRelationship: string
{
    use HasTranslatableLabel,
        Selectable;

    case Customer = 'customer';
    case Personal = 'personal';

    protected function getLabelKey(): string
    {
        return 'testimonial.relationship';
    }
}
