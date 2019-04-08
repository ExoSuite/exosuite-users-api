<?php

use Illuminate\Database\Seeder;

/**
 * Class DatabaseSeeder
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // first we need to populate role table
        $this->call(RolesSeeder::class);

        $this->call(UsersSeeder::class);

        $this->call(DashboardsSeeder::class);

        $this->call(ProfileRestrictionsSeeder::class);
    }
}
