<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'code';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['code', 'name', 'region', 'translations'];

    protected $casts = [
        'translations' => 'array',
    ];

    /**
     * Retrieve translated country name (fallback to English)
     */
    public function getTranslatedName(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        // Map to ISO 639-3
        $iso3 = config("app.locales.{$locale}")
            ?? config('app.locales.'.config('app.fallback_locale'))
            ?? 'eng';

        if ($iso3 === 'eng') {
            return $this->name;
        }

        return $this->translations[$iso3]['common']
            ?? $this->translations[$iso3]['official']
            ?? $this->name;
    }
}
