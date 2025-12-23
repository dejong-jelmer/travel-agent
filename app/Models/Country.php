<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Country extends Model
{
    use HasFactory;

    protected $perPage = 15;

    protected $fillable = ['name'];

    public $timestamps = false;

    public function trips(): BelongsToMany
    {
        return $this->belongsToMany(Trip::class);
    }
}
