<?php

namespace App\Providers;

use App\Services\AccessToken;
use App\Services\Cart\CartRepository;
use App\Services\GuestToken;
use Firebase\JWT\JWT;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->singleton('access-token', function ($app) {
			return new AccessToken($app->make('request'));
		});

		$this->app->singleton('guest-token', function ($app) {
			return new GuestToken($app->make('request'));
		});

		$this->app->singleton('cart', function ($app) {
			if (auth()->guest()) {
				$token = request()->get('Guest-Token', '');

				if (empty($token)) {
					throw new \Exception("Cart could not be created");
				}

				$jwt = JWT::decode($token, env('JWT_GUEST_SECRET'), ['HS256']);

				return new CartRepository(
					app()->make('cache'),
					$token,
					intval($jwt['exp'])
				);
			} else {
				$header = request()->header('Authorization', '');

				$token = Str::substr($header, 4);

				$jwt = JWT::decode($token, env('JWT_SECRET'), ['HS256']);

				return new CartRepository(
					app()->make('cache'),
					data_get($jwt, 'sub.id'),
					intval($jwt['exp'])
				);
			}
		});
	}
}
