<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RestaurantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['id' => 1, 'name' => 'Sicilija', 'address' => '50 Ivana Vujoševića', 'user_id' => 1, 'status' => true, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 2, 'name' => 'Pizzeria Buongiorno', 'address' => '43 Balšića', 'user_id' => 1, 'status' => true, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 3, 'name' => 'Pod Volat', 'address' => '1 Trg Vojvode Bećira Osmanagića', 'user_id' => 1, 'status' => true, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 4, 'name' => 'ULIX Gastro bar', 'address' => 'C6QM+FRG', 'user_id' => 1, 'status' => true, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ];

        DB::table('restaurants')->insert($data);
    }
}
