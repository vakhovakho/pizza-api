<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Services\Cart\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//
	}

	public function index()
	{
		return Order::query()
			->where('user_id', auth()->id())
			->latest()
			->with('products')
			->get();
	}

	public function create(Request $request)
	{
		$this->validate($request, [
			'name' => 'required',
			'address' => 'required',
			'email' => 'required|email',
			'number' => 'required',
		]);

		$data = $request->only([
			'name',
			'address',
			'email',
			'number',
			'comment',
		]);

		DB::transaction(function() use ($data) {
			/** @var Order $order */
			$order = Order::query()->create($data);

			/** @var \App\Services\Cart\CartItem[] $cartItems */
			$cartItems = Cart::all();
			$total = 0;
			foreach ($cartItems as $cartItem) {
				if ($cartItem->empty() || is_null($product = Product::query()->find($cartItem->id))) {
					Cart::remove($cartItem->id, $cartItem->selectedSize);
					continue;
				}
				/** @var Product $product */

				$price = $product->prices[$cartItem->selectedSize];
				$total += $price * $cartItem->amount;

				$order->products()->create([
					'name' => $product->name,
					'description' => $product->description,
					'size' => $cartItem->selectedSize,
					'price' => $price,
					'amount' => $cartItem->amount
				]);
			}

			$order->total = $total;
			if (!auth()->guest()) {
				$order->user_id = auth()->id();
			}
			$order->saveOrFail();
			Cart::delete();
		});

		return response()->json([], 204);
	}
}
