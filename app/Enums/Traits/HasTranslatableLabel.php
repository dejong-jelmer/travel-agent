<?php

namespace App\Enums\Traits;

trait HasTranslatableLabel
{
    public function label(): string
    {
        if (! defined(static::class.'::LABEL_KEY')) {
            throw new \LogicException(
                sprintf(
                    'Enum %s must define a private const LABEL_KEY to use HasTranslatableLabel trait',
                    static::class
                )
            );
        }

        return __(self::LABEL_KEY.".{$this->value}");
    }
}
