<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\User;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
		'address' => $faker->address,
		'number' => $faker->phoneNumber,
		'password' => \Illuminate\Support\Facades\Hash::make('password'),
    ];
});

$factory->define(\App\Models\Product::class, function (Faker $faker) {
	$price = $faker->randomFloat(2, 10, 50);
	return [
		'name' => "Pizza " . $faker->word,
		'image' => $faker->imageUrl(250, 160, 'food'),
		'description' => $faker->sentence,
		'price1' => $price,
		'price2' => round($price * 1.5, 2),
		'price3' => round($price * 1.8, 2),
	];
});

$factory->define(\App\Models\Order::class, function (Faker $faker) {
	$total = $faker->randomFloat(2, 30, 150);
	return [
		'user_id' => User::query()->find(2),
		'name' => $faker->name,
		'email' => $faker->email,
		'address' => $faker->address,
		'number' => $faker->phoneNumber,
		'comment' => $faker->sentence,
		'total' => $total
	];
});

$factory->define(\App\Models\OrderProduct::class, function (Faker $faker) {
	$price = $faker->randomFloat(2, 10, 50);
	$sizes = ['small', 'medium', 'large'];
	return [
		'order_id' => 1,
		'name' => "pizza " . $faker->word,
		'description' => $faker->sentence,
		'size' => $sizes[random_int(0, 2)],
		'price' => $price,
		'amount' => random_int(1, 5)
	];
});
