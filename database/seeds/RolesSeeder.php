<?php

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Enums\AdministratorPermissions;
use App\Enums\Roles;

/**
 * Class RolesSeeder
 */
class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create([
            'name' => Roles::ADMINISTRATOR,
            'permissions' => array_fill_keys(AdministratorPermissions::getValues(), true)
        ]);
    }
}
