<?php

namespace App\Http\Middleware;

use App\Services\Auth\JwtGuard;
use Closure;
use Exception;
use Firebase\JWT\ExpiredException;

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
			$auth = auth()->guard($guard ?: 'api');
			if ($auth instanceof JwtGuard) {
				$auth->jwt();
			} else {
				if ($auth->guest()) {
					return response()->json([
						'error' => 'Access Denied'
					], 401);
				}
			}
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
