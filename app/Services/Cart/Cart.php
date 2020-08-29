<?php

namespace App\Services\Cart;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void add(int $id, string $size, int $amount = null)
 * @method static void remove(int $id, string $size, int $amount = null): void
 * @method static void delete(): void
 * @method static \App\Services\CartItem[] all()
 *
 * @see \App\Services\CartRepository
 */
class Cart extends Facade
{
	protected static function getFacadeAccessor()
	{
		return 'cart';
	}
}
