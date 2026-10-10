<?php

namespace App\Enums\Trip;

use App\Enums\Traits\HasTranslatableLabel;
use App\Enums\Traits\Selectable;

/**
 * The part of the hero image that stays in view when the hero crops it, used as its CSS object-position. Each value
 * aligns that point of the image with the same point of the hero, so 0% keeps the left or top edge in view.
 *
 * The cases run row by row from top left to bottom right, the order of the 3x3 grid in the admin form.
 */
enum HeroFocus: string
{
    use HasTranslatableLabel,
        Selectable;

    case TopLeft = '0% 0%';
    case Top = '50% 0%';
    case TopRight = '100% 0%';
    case Left = '0% 50%';
    case Center = '50% 50%';
    case Right = '100% 50%';
    case BottomLeft = '0% 100%';
    case Bottom = '50% 100%';
    case BottomRight = '100% 100%';

    protected function getLabelKey(): string
    {
        return 'trip.hero_focus';
    }
}
