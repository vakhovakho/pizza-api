<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Illuminate\Http\Request;

class GuestToken
{
	protected $header;

	protected $request;

	public function __construct(Request $request, $header = 'Guest-Token')
	{
		$this->request = $request;
		$this->header = $header;
	}

	public function getHeaderName()
	{
		return $this->header;
	}

	public function generate($sub = [])
	{
		$payload = [
			'iss' => "guest",
			'sub' => empty($sub) ? sha1($this->request->getClientIp() . time() . rand(1000000, 9999999)) : $sub,
			'iat' => time(),
			'exp' => time() + 60 * 60
		];

		return JWT::encode($payload, env('JWT_GUEST_SECRET'));
	}

	public function fetch()
	{
		return JWT::decode(
			$this->request->header($this->header),
			env('JWT_GUEST_SECRET'),
			['HS256']
		);
	}

	public function resolve()
	{
		try {
			$this->fetch();

			return $this->request->header($this->header);
		} catch (\Throwable $e) {
			$token = $this->generate();
			$this->request->headers->set($this->header, $token);

			return $token;
		}
	}
}
