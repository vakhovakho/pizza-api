<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Routing\Controller as BaseController;

class AuthController extends BaseController
{
	/**
	 * Authenticate a user and return the token if the provided credentials are correct.
	 *
	 * @param Request     $request
	 *
	 * @return mixed
	 * @throws \Illuminate\Validation\ValidationException
	 */
	public function authenticate(Request $request)
	{
		$this->validate($request, [
			'email' => 'required|email',
			'password' => 'required'
		]);

		// Find the user by email
		/** @var User $user */
		$user = User::query()
			->where('email', $request->input('email'))
			->first();

		if (!$user) {
			// You wil probably have some sort of helpers or whatever
			// to make sure that you have the same response format for
			// different kind of responses. But let's return the
			// below response for now.
			return response()->json([
				'error' => 'Email does not exist.'
			], 400);
		}

		// Verify the password and generate the token
		if (Hash::check($request->input('password'), $user->password)) {
			return response()->json([
				'token' => $this->generateJWT($user)
			], 200);
		}

		// Bad Request response
		return response()->json([
			'error' => 'Email or password is wrong.'
		], 400);
	}

	public function register(RegisterRequest $request) {
		/** @var User $user */
		$user = User::query()->create([
			'name' => $request->name,
			'number' => $request->number,
			'address' => $request->address,
			'email' => $request->email,
			'password' => Hash::make($request->name)
		]);

		return response()->json([
			'token' => $this->generateJWT($user)
		], 200);
	}

	protected function generateJWT(User $user): string {
		return app('access-token')
			->generate($user->only(['id', 'email']));
	}
}