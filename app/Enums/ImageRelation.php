<?php

namespace App\Enums;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Symfony\Component\Validator\Exception\BadMethodCallException;

enum ImageRelation: string
{
    case Image = 'image';
    case Images = 'images';
    case FeaturedImage = 'featuredImage';

    public function getRelation(Model $model): MorphMany|MorphOne
    {
        $method = $this->value;
        if (! method_exists($model, $method)) {
            throw new BadMethodCallException(
                "Relation '{$method}' does not exist on ".get_class($model)
            );
        }

        return match ($this) {
            self::Image => $model->image(),
            self::Images => $model->images(),
            self::FeaturedImage => $model->featuredImage(),
        };
    }
}
