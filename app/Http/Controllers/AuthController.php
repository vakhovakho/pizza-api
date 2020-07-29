<?php

namespace App\Http\Controllers;

use App\Services\AccessToken;
use Validator;
use App\User;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Firebase\JWT\ExpiredException;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Routing\Controller as BaseController;

class AuthController extends BaseController
{
	/**
	 * Authenticate a user and return the token if the provided credentials are correct.
	 *
	 * @param Request     $request
	 * @param AccessToken $accessToken
	 *
	 * @return mixed
	 * @throws \Illuminate\Validation\ValidationException
	 */
	public function authenticate(Request $request, AccessToken $accessToken)
	{
		$this->validate($request, [
			'email' => 'required|email',
			'password' => 'required'
		]);

		// Find the user by email
		/** @var \App\User $user */
		$user = User::query()
			->where('email', $request->input('email'))
			->first();

		if (!$user) {
			// You wil probably have some sort of helpers or whatever
			// to make sure that you have the same response format for
			// differents kind of responses. But let's return the
			// below respose for now.
			return response()->json([
				'error' => 'Email does not exist.'
			], 400);
		}

		// Verify the password and generate the token
		if (Hash::check($request->input('password'), $user->password)) {
			return response()->json([
				'token' => $accessToken->generate(
					$user->only(['id', 'email'])
				)
			], 200);
		}

		// Bad Request response
		return response()->json([
			'error' => 'Email or password is wrong.'
		], 400);
	}
}