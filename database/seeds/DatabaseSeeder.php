<?php

use Illuminate\Database\Seeder;
use App\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $role = Role::create(['name' => 'Admin']);
        $role = Role::create(['name' => 'Designer']);
        $role = Role::create(['name' => 'Developer']);

        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin@pma1k1.com',
            'type' => 'Admin',
            'bio' => '',
            'password' => bcrypt('123456'),
        ])->assignRole('Admin');

    }
}
