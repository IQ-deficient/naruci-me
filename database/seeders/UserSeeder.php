<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
                                                    // adisspahic@hotmail.com
            ['id' => 1, 'name' => 'Adis', 'email' => 'adis@gmail.com', 'password' => Hash::make('adis12345'), 'type' => 'Admin', 'phone_number' => '067580130', 'status' => true, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 2, 'name' => 'Marko', 'email' => 'marko@gmail.com', 'password' => Hash::make('marko12345'), 'type' => 'Customer', 'phone_number' => '061234567', 'status' => true, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 3, 'name' => 'A', 'email' => 'a@gmail.com', 'password' => Hash::make('aaa12345'), 'type' => 'Admin', 'phone_number' => '067777777', 'status' => true, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 4, 'name' => 'Admin', 'email' => 'admin@gmail.com', 'password' => Hash::make('admin12345'), 'type' => 'Admin', 'phone_number' => '060000000', 'status' => true, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 5, 'name' => 'Customer', 'email' => 'customer@gmail.com', 'password' => Hash::make('customer12345'), 'type' => 'Customer', 'phone_number' => '061111111', 'status' => true, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ];

        DB::table('users')->insert($data);
    }
}
