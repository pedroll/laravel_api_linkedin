<?php

namespace App\Providers;

use App\Models\Passport\Client;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

//use App\Models\Passport\AuthCode;
//use App\Models\Passport\PersonalAccessClient;
//use App\Models\Passport\RefreshToken;
//use App\Models\Passport\Token;

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
        Passport::hashClientSecrets();
        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));
        Passport::useClientModel(Client::class);
//        Passport::useTokenModel(Token::class);
//        Passport::useAuthCodeModel(AuthCode::class);
//        Passport::usePersonalAccessClientModel(PersonalAccessClient::class);
        //
    }
}
