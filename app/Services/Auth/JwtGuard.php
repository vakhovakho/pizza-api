<?php

namespace App\Services\Auth;

use App\Services\AccessToken;
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

	protected $accessToken;

	public function __construct(UserProvider $provider, AccessToken $accessToken)
	{
		$this->setProvider($provider);
		$this->accessToken = $accessToken;
	}

	public function jwt()
	{
		if (!is_null($this->credentials)) {
			return $this->credentials;
		}

		return $this->credentials = $this->accessToken->fetch();
	}

	public function check()
	{
		try {
			return !is_null($this->id());
		} catch (\Throwable $e) {
			return false;
		}
	}

	public function id()
	{
		return intval(data_get($this->jwt(), 'sub.id'));
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
		try {
			$decodedJWT = $this->jwt();
		} catch (Exception $e) {
			return false;
		}

		if (is_nan(intval(data_get($decodedJWT, 'sub.id')))) {
			return false;
		}

		return false;
	}
}