<?php

namespace Database\Seeders;

use App\Models\UserRoles;
use Illuminate\Database\Seeder;

class UserRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        UserRoles::insert([
            ['name' => 'Admin'],
            ['name'=>'Librarian'],
            ['name'=>'User']
        ]);
    }
}
