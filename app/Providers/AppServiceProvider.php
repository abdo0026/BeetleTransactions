<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Schema;
use App\Traits\MorphMap;

class AppServiceProvider extends ServiceProvider
{
    use MorphMap;
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', -1);

        $morphMaps = $this->getMorphMap();
        Relation::morphMap($morphMaps);
        Schema::defaultStringLength(125);
    }
}
