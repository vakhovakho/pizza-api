<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{


	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name',
		'description',
		'price1',
		'price2',
		'price3'
	];

	protected $casts = [
		'price1' => 'float',
		'price2' => 'float',
		'price3' => 'float',
	];


}
