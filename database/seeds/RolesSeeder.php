<?php

use App\Enums\AdministratorPermissions;
use App\Enums\Roles;
use App\Models\Role;
use Illuminate\Database\Seeder;

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
