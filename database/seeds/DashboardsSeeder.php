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

        if ($lopez->exists() && Dashboard::whereOwnerId($lopez->first()->id)->doesntExist()) {
            Dashboard::create([
                'owner_id' => $lopez->first()->id,
            ]);
        }

        if ($dupil->exists() && Dashboard::whereOwnerId($dupil->first()->id)->doesntExist()) {
            Dashboard::create([
                'owner_id' => $dupil->first()->id,
            ]);
        }

        if ($eric->exists() && Dashboard::whereOwnerId($eric->first()->id)->doesntExist()) {
            Dashboard::create([
                'owner_id' => $eric->first()->id,
            ]);
        }

        if ($pierre->exists() && Dashboard::whereOwnerId($pierre->first()->id)->doesntExist()) {
            Dashboard::create([
                'owner_id' => $pierre->first()->id,
            ]);
        }

        if ($stan->exists() && Dashboard::whereOwnerId($stan->first()->id)->doesntExist()) {
            Dashboard::create([
                'owner_id' => $stan->first()->id,
            ]);
        }

        if ($renaud->exists() && Dashboard::whereOwnerId($renaud->first()->id)->doesntExist()) {
            Dashboard::create([
                'owner_id' => $renaud->first()->id,
            ]);
        }

        if ($mathilde->exists() && Dashboard::whereOwnerId($mathilde->first()->id)->doesntExist()) {
            Dashboard::create([
                'owner_id' => $mathilde->first()->id,
            ]);
        }

        if ($yassir->exists() && Dashboard::whereOwnerId($yassir->first()->id)->doesntExist()) {
            Dashboard::create([
                'owner_id' => $yassir->first()->id,
            ]);
        }
    }
}
