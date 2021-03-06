<?php

use App\Enums\Roles;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Class UsersSeeder
 */
class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (User::whereEmail('loic.lopez@exosuite.fr')->doesntExist()) {
            $loic_lopez = User::create([
                'first_name' => 'Loïc',
                'last_name' => 'Lopez',
                'email' => 'loic.lopez@exosuite.fr',
                'password' => '2tzFySjdWLHKxpD7',
            ]);
            $loic_lopez->addRole(Roles::ADMINISTRATOR);
        }

        if (User::whereEmail('pierre.piazza@exosuite.fr')->doesntExist()) {
            $pierre_piazza = User::create([
                'first_name' => 'Pierre',
                'last_name' => 'Piazza',
                'email' => 'pierre.piazza@exosuite.fr',
                'password' => 'ypAx6938tadxhhTw',
            ]);
            $pierre_piazza->addRole(Roles::ADMINISTRATOR);
        }

        if (User::whereEmail('loic.dupil@exosuite.fr')->doesntExist()) {
            $loic_dupil = User::create([
                'first_name' => 'Loïc',
                'last_name' => 'Dupil',
                'email' => 'loic.dupil@exosuite.fr',
                'password' => '7s9nXtg865k49xp8',
            ]);
            $loic_dupil->addRole(Roles::ADMINISTRATOR);
        }

        if (User::whereEmail('yassir.jabbari@exosuite.fr')->doesntExist()) {
            $yassir = User::create([
                'first_name' => 'Yassir',
                'last_name' => 'Jabbari',
                'email' => 'yassir.jabbari@exosuite.fr',
                'password' => 'HHHSW9UuwPerhJWV',
            ]);
            $yassir->addRole(Roles::ADMINISTRATOR);
        }

        if (User::whereEmail('renaud.juliani@exosuite.fr')->doesntExist()) {
            $renaud = User::create([
                'first_name' => 'Renaud',
                'last_name' => 'Juliani',
                'email' => 'renaud.juliani@exosuite.fr',
                'password' => 'ekcRveEvRLS7nn9b',
            ]);
            $renaud->addRole(Roles::ADMINISTRATOR);
        }

        if (User::whereEmail('stan.deneubourg@exosuite.fr')->doesntExist()) {
            $stan = User::create([
                'first_name' => 'Stan',
                'last_name' => 'Deneubourg',
                'email' => 'stan.deneubourg@exosuite.fr',
                'password' => 'BNRT7gjRpNaS8e6d',
            ]);
            $stan->addRole(Roles::ADMINISTRATOR);
        }

        if (User::whereEmail('eric.deschodt@exosuite.fr')->doesntExist()) {
            $eric = User::create([
                'first_name' => 'Eric',
                'last_name' => 'Deschodt',
                'email' => 'eric.deschodt@exosuite.fr',
                'password' => 'gzY6Qx8CsS97kcXG',
            ]);
            $eric->addRole(Roles::ADMINISTRATOR);
        }

        if (User::whereEmail('mathilde.charpiot@exosuite.fr')->doesntExist()) {
            $mathilde = User::create([
                'first_name' => 'Mathilde',
                'last_name' => 'Charpiot',
                'email' => 'mathilde.charpiot@exosuite.fr',
                'password' => 'HDSCQey2cNrMjR3V',
            ]);
            $mathilde->addRole(Roles::ADMINISTRATOR);
        }
    }
}
