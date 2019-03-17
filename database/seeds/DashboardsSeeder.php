<?php

use App\Models\Dashboard;
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

        if ($lopez->exists() && $lopez->dashboard()->doesntExist()) {
            $lopez->dashboard()->create();
        }

        if ($dupil->exists() && $dupil->dashboard()->doesntExist()) {
            $dupil->dashboard()->create();
        }

        if ($eric->exists() && $eric->dashboard()->doesntExist()) {
            $eric->dashboard()->create();
        }

        if ($pierre->exists() && $pierre->dashboard()->doesntExist()) {
            $pierre->dashboard()->create();
        }

        if ($stan->exists() && $stan->dashboard()->doesntExist()) {
            $stan->dashboard()->create();
        }

        if ($renaud->exists() && $renaud->dashboard()->doesntExist()) {
            $renaud->dashboard()->create();
        }

        if ($mathilde->exists() && $mathilde->dashboard()->doesntExist()) {
            $mathilde->dashboard()->create();
        }

        if ($yassir->exists() && $yassir->dashboard()->doesntExist()) {
            $yassir->dashboard()->create();
        }
    }
}
