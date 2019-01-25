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
            'password' => '2tzFySjdWLHKxpD7'
        ]);
        $loic_lopez->addRole(Roles::ADMINISTRATOR);

        $pierre_piazza = User::create([
            'first_name' => 'Pierre',
            'last_name' => 'Piazza',
            'email' => 'pierre.piazza@exosuite.fr',
            'password' => 'ypAx6938tadxhhTw'
        ]);
        $pierre_piazza->addRole(Roles::ADMINISTRATOR);

        $loic_dupil = User::create([
            'first_name' => 'Loïc',
            'last_name' => 'Dupil',
            'email' => 'loic.dupil@exosuite.fr',
            'password' => '7s9nXtg865k49xp8'
        ]);
        $loic_dupil->addRole(Roles::ADMINISTRATOR);

        $yassir = User::create([
            'first_name' => 'Yassir',
            'last_name' => 'Jabbari',
            'email' => 'yassir.jabbari@exosuite.fr',
            'password' => 'HHHSW9UuwPerhJWV'
        ]);
        $yassir->addRole(Roles::ADMINISTRATOR);

        $renaud = User::create([
            'first_name' => 'Renaud',
            'last_name' => 'Juliani',
            'email' => 'renaud.juliani@exosuite.fr',
            'password' => 'ekcRveEvRLS7nn9b'
        ]);
        $renaud->addRole(Roles::ADMINISTRATOR);

        $stan = User::create([
            'first_name' => 'Stan',
            'last_name' => 'Deneubourg',
            'email' => 'stan.deneubourg@exosuite.fr',
            'password' => 'BNRT7gjRpNaS8e6d'
        ]);
        $stan->addRole(Roles::ADMINISTRATOR);

        $eric = User::create([
            'first_name' => 'Eric',
            'last_name' => 'Deschodt',
            'email' => 'eric.deschodt@exosuite.fr',
            'password' => 'gzY6Qx8CsS97kcXG'
        ]);
        $eric->addRole(Roles::ADMINISTRATOR);

        $mathilde = User::create([
            'first_name' => 'Mathilde',
            'last_name' => 'Charpiot',
            'email' => 'mathilde.charpiot@exosuite.fr',
            'password' => 'HDSCQey2cNrMjR3V'
        ]);
        $mathilde->addRole(Roles::ADMINISTRATOR);
    }
}
