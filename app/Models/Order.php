<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{


	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'address',
		'email',
		'number',
		'total'
	];

	protected $casts = [
		'total' => 'float'
	];

	public function user() {
		return $this->belongsTo(User::class);
	}

	public function products() {
		return $this->hasMany(OrderProduct::class);
	}


}
