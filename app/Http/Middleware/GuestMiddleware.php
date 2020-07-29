<?php

namespace App\Http\Middleware;

use Closure;

class GuestMiddleware
{
	/**
	 * @param \Illuminate\Http\Request $request
	 * @param Closure                  $next
	 *
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		/** @var \App\Services\GuestToken $guestToken */
		$guestToken = app('guest-token');
		$token = $guestToken->resolve(); // must be resolved before response
		$response = $next($request);
		$response->header($guestToken->getHeaderName(), $token);

		return $response;
	}
}
