<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['id' => 1, 'name' => 'Krempita', 'description' => 'Lisnate kore, bogati francuski krem i višnja. Probajte i osjetite kako mali dodatak unosi veliku promjenu.', 'price'=> 3.1, 'restaurant_id' => 1, 'user_id' => 1, 'status' => true, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 2, 'name' => 'Piadina Diavola', 'description' => 'kulen, sir, pavlaka, zelena salata, paradajz, dresing', 'price'=> 4.2, 'restaurant_id' => 1, 'user_id' => 1, 'status' => true, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 3, 'name' => 'Corleone', 'description' => 'Šunka, sir, sos', 'price'=> 3.6, 'restaurant_id' => 1, 'user_id' => 1, 'status' => true, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 4, 'name' => 'BURGER (CLASSIC)', 'description' => 'Goveđi burger, zelena salata, kiseli krastavci, BBQ sos', 'price'=> 2.2, 'restaurant_id' => 1, 'user_id' => 1, 'status' => true, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 12, 'name' => 'BURGER (Premium)', 'description' => 'Goveđi burger, kiseli krastavci, BBQ sos, premium pomfrit, tajni sos', 'price'=> 5.2, 'restaurant_id' => 1, 'user_id' => 1, 'status' => false, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 5, 'name' => 'Lovačka pizza', 'description' => 'Pelat, kobasica, slanina, luk, trapist, bijeli luk, origano', 'price'=> 6.1, 'restaurant_id' => 2, 'user_id' => 1, 'status' => true, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 6, 'name' => 'Pizza Margarita', 'description' => 'Pelat, trapist, origano', 'price'=> 3.9, 'restaurant_id' => 2, 'user_id' => 1, 'status' => true, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 7, 'name' => 'Pizza Capriciossa', 'description' => 'Pelat, trapist, šunka, šampinjoni, origano', 'price'=> 4.9, 'restaurant_id' => 2, 'user_id' => 1, 'status' => true, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 8, 'name' => 'Cevapi 10 komada', 'description' => 'Junece meso', 'price'=> 2.9, 'restaurant_id' => 3, 'user_id' => 1, 'status' => true, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 9, 'name' => 'Mijesano meso', 'description' => '1kg mijesanog mesa', 'price'=> 10.9, 'restaurant_id' => 3, 'user_id' => 1, 'status' => true, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 10, 'name' => 'Cezar salata 350gr', 'description' => 'Pileći file, zelena salata, krutoni,cezar preliv, slanina, parmezan, jaja od prepelice', 'price'=> 4.9, 'restaurant_id' => 4, 'user_id' => 1, 'status' => true, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 11, 'name' => 'ULIX rižoto 300gr', 'description' => 'Biftek, grašak, piletina, soja sos, spanać, marinada sa bijelim lukom', 'price'=> 7.9, 'restaurant_id' => 4, 'user_id' => 1, 'status' => true, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ];

        DB::table('products')->insert($data);
    }
}
