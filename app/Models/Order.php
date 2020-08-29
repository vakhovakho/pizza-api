<?php

namespace App\Models;

use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string   address
 * @property string   email
 * @property string   number
 * @property string   name
 * @property string   comment
 * @property float    total
 * @property User     user
 * @property int|null user_id
 */
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
		'comment',
		'name',
		'total'
	];

	protected $appends = [
		'date'
	];

	protected $casts = [
		'total' => 'float'
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function products()
	{
		return $this->hasMany(OrderProduct::class);
	}

	public function getDateAttribute(){
		return $this->created_at->format('m/d/Y');
	}
}
