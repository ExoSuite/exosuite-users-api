<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Enums\Roles;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /** @var User $loic_lopez */
        $loic_lopez = User::create([
            'first_name' => 'Loïc',
            'last_name' => 'Lopez',
            'email' => 'loic.lopez@exosuite.fr',
            'password' => 'azerty'
        ]);

        $loic_lopez->addRole(Roles::ADMINISTRATOR);
    }
}
