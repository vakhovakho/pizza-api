<?php

return [
	'defaults' => [
		'guard' => 'api',
	],
	'guards' => [
		'api' => [
			'driver' => 'jwt',
			'provider' => 'users',
		]
	],
	'providers' => [
		'users' => [
			'driver' => 'eloquent',
			'model' => App\User::class,
		],
	],
];