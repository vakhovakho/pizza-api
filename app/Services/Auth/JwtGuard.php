<?php

namespace App\Services\Auth;

use App\User;
use Exception;
use Firebase\JWT\JWT;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class JwtGuard implements Guard
{
	use GuardHelpers;

	protected $credentials = null;

	protected $request;

	public function __construct(UserProvider $provider, Request $request)
	{
		$this->setProvider($provider);
		$this->request = $request;
	}

	public function jwt()
	{
		if (!is_null($this->credentials)) {
			return $this->credentials;
		}

		$credentials = JWT::decode($this->getTokenForRequest(), env('JWT_SECRET'), ['HS256']);

		return $this->credentials = $credentials;
	}

	public function check()
	{
		return !is_null($this->id());
	}

	public function id()
	{
		return intval(data_get($this->jwt(), 'sub.id'));
	}


	/**
	 * Get the token for the current request.
	 *
	 * @return string
	 */
	public function getTokenForRequest()
	{
		$header = $this->request->header('Authorization', '');

		if (Str::startsWith($header, 'JWT ')) {
			return Str::substr($header, 4);
		}

		return false;
	}

	/**
	 * Get the currently authenticated user.
	 *
	 * @return \Illuminate\Contracts\Auth\Authenticatable|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
	 */
	public function user()
	{
		return $this->getProvider()->retrieveById($this->id());
	}

	/**
	 * Validate a user's credentials.
	 *
	 * @param array $credentials
	 *
	 * @return bool
	 */
	public function validate(array $credentials = [])
	{
		if (empty($credentials['jwt'])) {
			return false;
		}

		try {
			$decodedJWT = JWT::decode($this->getTokenForRequest(), env('JWT_SECRET'), ['HS256']);
		} catch (Exception $e) {
			return false;
		}

		if (is_nan(intval(data_get($decodedJWT, 'sub.id')))) {
			return false;
		}

		return false;
	}
}