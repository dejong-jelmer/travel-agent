<?php

namespace App\Models;

use App\Enums\SettingKey;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    public $incrementing = false;

    public $timestamps = false;

    protected $primaryKey = 'key';

    protected $keyType = 'string';

    protected $fillable = ['key', 'value'];

    public static function get(SettingKey $key, mixed $default = null): mixed
    {
        return static::where('key', $key->value)->value('value') ?? $default;
    }

    public static function set(SettingKey $key, mixed $value): void
    {
        if ($value === null) {
            static::where('key', $key->value)->delete();

            return;
        }

        static::updateOrCreate(['key' => $key->value], ['value' => $value]);
    }
}
