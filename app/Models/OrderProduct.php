<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{


	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name',
		'description',
		'size',
		'price'
	];

	protected $casts = [
		'price' => 'float'
	];

	public function order() {
		return $this->belongsTo(Order::class);
	}


}
