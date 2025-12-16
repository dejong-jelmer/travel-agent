<?php

namespace App\Enums\Traits;

trait HasTranslatableLabel
{
    /**
     * Get the translation key prefix for this enum
     */
    abstract protected function getLabelKey(): string;

    public function label(): string
    {
        return __($this->getLabelKey().".{$this->value}");
    }
}
