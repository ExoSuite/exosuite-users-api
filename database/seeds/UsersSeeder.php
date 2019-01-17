<?php

use App\Enums\Roles;
use App\Models\User;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $loic_lopez = User::create([
            'first_name' => 'Loïc',
            'last_name' => 'Lopez',
            'email' => 'loic.lopez@exosuite.fr',
            'password' => 'azerty'
        ]);
        $loic_lopez->addRole(Roles::ADMINISTRATOR);

        $pierre_piazza = User::create([
            'first_name' => 'Pierre',
            'last_name' => 'Piazza',
            'email' => 'pierre.piazza@exosuite.fr',
            'password' => 'test123'
        ]);
        $pierre_piazza->addRole(Roles::ADMINISTRATOR);

        $loic_dupil = User::create([
            'first_name' => 'Loïc',
            'last_name' => 'Dupil',
            'email' => 'loic.dupil@exosuite.fr',
            'password' => 'azerty'
        ]);
        $loic_dupil->addRole(Roles::ADMINISTRATOR);

        $yassir = User::create([
            'first_name' => 'Yassir',
            'last_name' => 'Jabbari',
            'email' => 'yassir.jabbari@exosuite.fr',
            'password' => 'azerty'
        ]);
        $yassir->addRole(Roles::ADMINISTRATOR);

        $renaud = User::create([
            'first_name' => 'renaud',
            'last_name' => 'juliani',
            'email' => 'renal.juliani@exosuite.fr',
            'password' => 'azerty'
        ]);
        $renaud->addRole(Roles::ADMINISTRATOR);

        $stan = User::create([
            'first_name' => 'stan',
            'last_name' => 'deneubourg',
            'email' => 'stan.deneubourg@exosuite.fr ',
            'password' => 'azerty'
        ]);
        $stan->addRole(Roles::ADMINISTRATOR);
    }
}
