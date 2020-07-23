<?php

namespace App\Http\Controllers;

use App\Models\Order;

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

    public function index() {
    	return Order::query()
			->where('user_id', auth()->id())
			->with('products')
			->get();
	}
}
