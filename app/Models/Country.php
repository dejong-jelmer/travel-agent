<?php

namespace App\Models;

use App\Models\Traits\Sortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Country extends Model
{
    use HasFactory,
        Sortable;

    protected $perPage = 15;

    protected $fillable = ['name'];

    public $timestamps = false;

    protected $searchable = ['name'];

    protected $sortable = ['id', 'name'];

    public function trips(): BelongsToMany
    {
        return $this->belongsToMany(Trip::class);
    }
}
