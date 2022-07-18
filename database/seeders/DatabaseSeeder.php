<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
         \App\Models\User::factory(10)->create();
         \App\Models\Author::factory(10)->create();
         \App\Models\Publisher::factory(10)->create();
         \App\Models\Category::factory(10)->create();
         \App\Models\Books::factory(10)->create();
         \App\Models\UserBook::factory(10)->create();
    }
}
