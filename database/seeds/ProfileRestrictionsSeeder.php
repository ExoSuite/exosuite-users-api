<?php

use App\Models\User;
use Illuminate\Database\Seeder;

class ProfileRestrictionsSeeder extends Seeder
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

        if ($lopez->exists() && $lopez->first()->profileRestrictions()->doesntExist()) {
            $lopez->first()->profileRestrictions()->create();
        }

        if ($dupil->exists() && $dupil->first()->profileRestrictions()->doesntExist()) {
            $dupil->first()->profileRestrictions()->create();
        }

        if ($eric->exists() && $eric->first()->profileRestrictions()->doesntExist()) {
            $eric->first()->profileRestrictions()->create();
        }

        if ($pierre->exists() && $pierre->first()->profileRestrictions()->doesntExist()) {
            $pierre->first()->profileRestrictions()->create();
        }

        if ($stan->exists() && $stan->first()->profileRestrictions()->doesntExist()) {
            $stan->first()->profileRestrictions()->create();
        }

        if ($renaud->exists() && $renaud->first()->profileRestrictions()->doesntExist()) {
            $renaud->first()->profileRestrictions()->create();
        }

        if ($mathilde->exists() && $mathilde->first()->profileRestrictions()->doesntExist()) {
            $mathilde->first()->profileRestrictions()->create();
        }

        if ($yassir->exists() && $yassir->first()->profileRestrictions()->doesntExist()) {
            $yassir->first()->profileRestrictions()->create();
        }
    }
}
