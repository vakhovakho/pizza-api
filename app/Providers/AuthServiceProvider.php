<?php

namespace App\Providers;

use App\Services\Auth\JwtGuard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
		Auth::extend('jwt', function ($app, $name, array $config) {
			return new JwtGuard(
				Auth::createUserProvider($config['provider']),
				$app->make('access-token')
			);
		});
    }
}
