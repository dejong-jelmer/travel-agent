<?php

namespace App\Enums;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

enum ImageRelation: string
{
    case Image = 'image';
    case Images = 'images';
    case FeaturedImage = 'featuredImage';

    public function getRelation(Model $model): MorphMany|MorphOne
    {
        return match ($this) {
            self::Image => $model->image(),
            self::Images => $model->images(),
            self::FeaturedImage => $model->featuredImage(),
        };
    }
}
