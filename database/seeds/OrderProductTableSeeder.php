<?php

use Illuminate\Database\Seeder;

class OrderProductTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		factory(\App\Models\OrderProduct::class, 3)->create(['order_id' => \App\Models\Order::query()->find(1)]);
		factory(\App\Models\OrderProduct::class, 3)->create(['order_id' => \App\Models\Order::query()->find(2)]);
    }
}
