<?php

use App\Models\User;
use Illuminate\Database\Seeder;

class DashboardsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $lopez = User::whereEmail('loic.lopez@exosuite.fr');
        $dupil = User::whereEmail('loic.dupil@exosuite.fr');
        $eric = User::whereEmail('eric.deschodt@exosuite.fr');
        $pierre = User::whereEmail('pierre.piazza@exosuite.fr');
        $stan = User::whereEmail('stanislas.deneubourg@exosuite.fr');
        $renaud = User::whereEmail('renaud.juliani@exosuite.fr');
        $mathilde = User::whereEmail('mathilde.charpiot@exosuite.fr');
        $yassir = User::whereEmail('yassir.jabbari@exosuite.fr');

        if ($lopez->exists() && $lopez->first()->dashboard()->doesntExist()) {
            $lopez->first()->dashboard()->create();
        }

        if ($dupil->exists() && $dupil->first()->dashboard()->doesntExist()) {
            $dupil->first()->dashboard()->create();
        }

        if ($eric->exists() && $eric->first()->dashboard()->doesntExist()) {
            $eric->first()->dashboard()->create();
        }

        if ($pierre->exists() && $pierre->first()->dashboard()->doesntExist()) {
            $pierre->first()->dashboard()->create();
        }

        if ($stan->exists() && $stan->first()->dashboard()->doesntExist()) {
            $stan->first()->dashboard()->create();
        }

        if ($renaud->exists() && $renaud->first()->dashboard()->doesntExist()) {
            $renaud->first()->dashboard()->create();
        }

        if ($mathilde->exists() && $mathilde->first()->dashboard()->doesntExist()) {
            $mathilde->first()->dashboard()->create();
        }

        if ($yassir->exists() && $yassir->first()->dashboard()->doesntExist()) {
            $yassir->first()->dashboard()->create();
        }
    }
}
