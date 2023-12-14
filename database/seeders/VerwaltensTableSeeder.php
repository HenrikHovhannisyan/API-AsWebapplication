<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VerwaltensTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $verwaltenData = [
            [
                'user_id' => 1,
                'stufe' => 1,
                'punkte' => 100,
            ],
            // Add more data as needed
        ];

        // Insert data into the 'verwaltens' table
        DB::table('verwaltens')->insert($verwaltenData);
    }
}
