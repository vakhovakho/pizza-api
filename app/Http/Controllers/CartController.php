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
		$products = [];
		/** @var CartItem[] $cartItems */
		$cartItems = Cart::all();
		$total = 0;
		foreach ($cartItems as $cartItem) {
			if ($cartItem->empty() || is_null($product = Product::query()->find($cartItem->id))) {
				Cart::remove($cartItem->id, $cartItem->selectedSize);
				continue;
			}

			$total += $product->prices[$cartItem->selectedSize] * $cartItem->amount;

			$products[] = [
				'product' => $product->toArray(),
				'selectedSize' => $cartItem->selectedSize,
				'amount' => $cartItem->amount
			];
		}

		$data = [
			'items' => $products,
			'total' => round($total, 2)
		];

		return response()->json(['data' => $data], 200);
	}

	public function add(Request $request)
	{
		$this->validate($request, [
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
