<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use DB;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;


class DatabaseSeeder extends Seeder
{
    
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('users')->truncate(); // This line clears the table

        // User::factory(10)->create();

        $user=User::factory()->create([
            'name' => 'Bryan Wachira',
            'email' => 'wachirabryan12@gmail.com',
        ]);
        $role = Role::create(['name' => 'Admin']);
        $user->assignRole($role);

    }


}
