<?php

namespace App\Travel;

use App\Travel\Models\Accommodation;
use App\Travel\Models\Journey;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class TravelServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Relation::morphMap([
            'journey' => Journey::class,
            'accommodation' => Accommodation::class,
        ]);

        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        $this->loadRoutesFrom(__DIR__.'/Http/routes.php');
    }
}
