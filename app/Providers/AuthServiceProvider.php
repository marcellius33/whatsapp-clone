<?php

namespace App\Providers;

use App\Models\Passport\OauthClient;
use App\Models\Passport\OauthPersonalAccessClient;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerPolicies();

        Passport::enablePasswordGrant();
        Passport::hashClientSecrets();
        Passport::tokensExpireIn(now()->addDay());
        Passport::refreshTokensExpireIn(now()->addMonth());
        Passport::personalAccessTokensExpireIn(now()->addYear());

        Passport::useClientModel(OauthClient::class);
        Passport::usePersonalAccessClientModel(OauthPersonalAccessClient::class);
    }
}
