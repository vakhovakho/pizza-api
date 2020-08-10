<?php

namespace App\Http\Requests;

use \Laravel\Lumen\Http\Request;

/**
 * @property-read string name
 * @property-read string number
 * @property-read string address
 * @property-read string email
 * @property-read string password
 */
abstract class FormRequest extends Request
{
	public function rules()
	{
		return [
			'name' => 'required|max:255',
			'number' => 'required|max:255',
			'address' => 'required|max:255',
			'email' => 'required|email|unique:users',
			'password' => 'required|max:255'
		];
	}
}
