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

    /** @return array<string, string> */
    public static function labels(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $section) => [$section->value => $section->label()])
            ->all();
    }
}
