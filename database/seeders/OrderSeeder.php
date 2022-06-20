<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['id' => 1, 'user_id' => 1, 'bill' => 23.6, 'status' => false, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 2, 'user_id' => 1, 'bill' => 12, 'status' => false, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ];

        DB::table('orders')->insert($data);
    }
}
