<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class AccessToken
{
	protected $header;

	protected $request;

	public function __construct(Request $request, $header = 'Authorization')
	{
		$this->request = $request;
		$this->header = $header;
	}

	public function generate($sub = [])
	{
		$payload = [
			'iss' => "access",
			'sub' => $sub,
			'iat' => time(),
			'exp' => Carbon::now()->addDay()->getTimestamp()
		];

		return JWT::encode($payload, env('JWT_SECRET'));
	}

	public function fetch()
	{
		$authHeader = $this->request->header($this->header);
		if(!Str::startsWith($authHeader, 'JWT ')) {
			throw new \Exception('Invalid Auth Token Type');
		}

		return JWT::decode(
			Str::substr($authHeader, 4),
			env('JWT_SECRET'),
			['HS256']
		);
	}

	public function resolve()
	{
		try {
			return $this->fetch();
		} catch (\Throwable $e) {
			$token = $this->generate();
			$this->request->headers->set($this->header, $token);

			return $this->fetch();
		}
	}
}
