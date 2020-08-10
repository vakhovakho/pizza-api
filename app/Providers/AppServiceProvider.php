<?php

namespace App\Providers;

use App\Http\Requests\FormRequest;
use App\Services\AccessToken;
use App\Services\Cart\CartRepository;
use App\Services\GuestToken;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
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
				$key = $jwt->sub;
				$ttl = intval($jwt->exp);
			} else {
				$key = data_get($app->make('access-token')->fetch(), 'sub.id');
				$ttl = null;
			}

			// better if it will be redis store
			return new CartRepository($app->make('cache')->store(), $key, $ttl);
		});

		$this->app->afterResolving(FormRequest::class, function (FormRequest $request, Application $app) {
			FormRequest::createFrom($app->make('request'), $request);

			$validator = Validator::make(
				$request->all(),
				$request->rules(),
				method_exists($request, 'messages') ? call_user_func([$request, 'messages']) : []
			);

			if ($validator->fails()) {
//				throw new ValidationException($validator);
				throw new HttpResponseException(
					response()->json(
						[
							'message' => 'The given data was invalid.',
							'errors' => $validator->errors(),
						],
						422
					)
				);
			}
		});
	}
}
