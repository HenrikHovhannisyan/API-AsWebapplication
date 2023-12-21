<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Verwalten;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\User::factory()->create([
            'name' => 'Admin',
            'first_name' => 'First name',
            'last_name' => 'Last name',
            'email' => 'admin@gmail.com',
            'password' => bcrypt(12345678),
        ]);

        $user = User::where('email', 'admin@gmail.com')->first();

        Verwalten::create([
            'user_id' => $user->id,
            'stufe' => 0,
            'punkte' => 0,
        ]);
    }
}
