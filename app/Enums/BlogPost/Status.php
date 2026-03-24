<?php

namespace App\Enums\BlogPost;

use App\Enums\Traits\HasTranslatableLabel;
use App\Enums\Traits\Selectable;

enum Status: string
{
    use HasTranslatableLabel,
        Selectable;

    case Draft = 'draft';
    case Published = 'published';

    protected function getLabelKey(): string
    {
        return 'enum.blog_post';
    }
}
