<?php

namespace App\Providers;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class AliasServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $loader = AliasLoader::getInstance();
        //$loader->alias('Onesignal', OneSignalFacade::class );
        $loader->alias('OneSignal', \Ladumor\OneSignal\OneSignal::class,);

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
