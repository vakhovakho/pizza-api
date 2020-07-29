<?php

namespace App\Providers;

use App\Services\AccessToken;
use App\Services\Cart\CartRepository;
use App\Services\GuestToken;
use Firebase\JWT\JWT;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Lumen\Application;

class AppServiceProvider extends ServiceProvider
{
	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->singleton('access-token', function (Application $app) {
			return new AccessToken($app->make('request'));
		});

		$this->app->singleton('guest-token', function (Application $app) {
			return new GuestToken($app->make('request'));
		});

		$this->app->singleton('cart', function (Application $app) {
			if (auth()->guest()) {
				$jwt = $app->make('guest-token')->fetch();
				$key = $jwt['sub'];
				$ttl = intval($jwt['exp']);
			} else {
				$key = data_get($app->make('access-token')->fetch(), 'sub.id');
				$ttl = null;
			}

			return new CartRepository($app->make('cache'), $key, $ttl);
		});
	}
}
