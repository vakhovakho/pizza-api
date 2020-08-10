<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property float price1
 * @property float price2
 * @property float price3
 * @property float[] prices
 */
class Product extends Model
{
	protected $fillable = [
		'name',
		'image',
		'description',
		'price1',
		'price2',
		'price3'
	];

	protected $appends = [
		'prices'
	];

	protected $casts = [
		'price1' => 'float',
		'price2' => 'float',
		'price3' => 'float',
	];

	protected $hidden = [
		'price1', 'price2', 'price3'
	];

	public function getPricesAttribute() {
		return [
			'small' => $this->price1,
			'medium' => $this->price2,
			'large' => $this->price3,
		];
	}
}
