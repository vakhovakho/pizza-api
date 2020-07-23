<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use App\User;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Illuminate\Support\Str;

class JwtMiddleware
{
	/**
	 * @param \Illuminate\Http\Request $request
	 * @param Closure                  $next
	 * @param null                     $guard
	 *
	 * @return \Illuminate\Http\JsonResponse|mixed
	 */
	public function handle($request, Closure $next, $guard = null)
	{
		try {
			auth()->guard($guard ?: 'api')->check();
		} catch (ExpiredException $e) {
			return response()->json([
				'error' => 'Provided token is expired.'
			], 419);
		} catch (Exception $e) {
			return response()->json([
				'error' => 'An error while decoding token.'
			], 401);
		}

		return $next($request);
	}
}