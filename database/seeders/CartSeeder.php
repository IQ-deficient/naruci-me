<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['id' => 1, 'product_id' => 4, 'order_id' => 1, 'quantity' => 2, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 2, 'product_id' => 1, 'order_id' => 1, 'quantity' => 3, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 3, 'product_id' => 11, 'order_id' => 1, 'quantity' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 4, 'product_id' => 5, 'order_id' => 2, 'quantity' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 5, 'product_id' => 6, 'order_id' => 2, 'quantity' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ];

        DB::table('carts')->insert($data);
    }
}
