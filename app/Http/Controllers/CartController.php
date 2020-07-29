<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\Cart\Cart;
use App\Services\Cart\CartItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CartController extends Controller
{
	public function index()
	{
		$items = [];
		/** @var CartItem[] $cartItems */
		$cartItems = Cart::all();
		foreach ($cartItems as $cartItem) {
			if ($cartItem->empty() || is_null($product = Product::query()->find($cartItem->id))) {
				Cart::remove($cartItem->id, $cartItem->size);
				continue;
			}

			$items[] = [
				'product' => $product->toArray(),
				'size' => $cartItem->size,
				'amount' => $cartItem->amount
			];
		}

		return response()->json(['data' => $items], 200);
	}

	public function add(Request $request)
	{
		$request->validate([
			'id' => 'required',
			'size' => [
				'required',
				Rule::in(['small', 'medium', 'large'])
			]
		]);

		Cart::add($request->get('id'), $request->get('size'), $request->get('amount', 1));

		return response('', 204);
	}

	public function remove(Request $request)
	{
		$request->validate([
			'id' => 'required',
			'size' => [
				'required',
				Rule::in(['small', 'medium', 'large'])
			]
		]);

		Cart::remove($request->get('id'), $request->get('size'));

		return response('', 204);
	}

	public function sub(Request $request)
	{
		$request->validate([
			'id' => 'required',
			'size' => [
				'required',
				Rule::in(['small', 'medium', 'large'])
			]
		]);

		Cart::remove($request->get('id'), $request->get('size'), $request->get('amount', 1));

		return response('', 204);
	}
}
