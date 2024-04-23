<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;


class AppServiceProvider extends ServiceProvider
{
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
        Passport::enablePasswordGrant();
        //todo
        #Passport::hashClientSecrets();
        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(100));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));
        //Berkayk\OneSignal\OneSignalServiceProvider::class;

        #Schema::defaultStringLength(191);
//        AliasLoader::getInstance([
//            'OneSignal' => Berkayk\OneSignal\OneSignalFacade::class,
//        ]);
    }
}
