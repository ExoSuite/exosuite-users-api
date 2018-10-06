<?php

namespace App\Http\Controllers;

use Artisan;

class StagingController extends Controller
{
    public function get()
    {
        // create new passport client
        Artisan::call('passport:client', ['--name' => 'Exosuite Website', '--password' => true]);
        // get output from previous Artisan::call
        $output = Artisan::output();
        // remove \n and split output into an array
        $parsedOutput = explode(PHP_EOL, $output);
        array_filter($parsedOutput, function ($item) use (&$client_id, &$client_secret) {
            // check if $item includes Client ID
            if (strpos($item, 'Client ID') !== false) {
                // get client id from array string
                list($junk, $client_id) = explode(": ", $item);
            }
            // check if $item includes Client Secret
            if (strpos($item, 'Client Secret') !== false) {
                // get client secret from array string
                list($junk, $client_secret) = explode(": ", $item);
            }
        });

        return $this->created([
            'client_id' => $client_id,
            'client_secret' => $client_secret
        ]);
    }
}
