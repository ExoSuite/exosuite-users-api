<?php

namespace App\Http\Controllers;

use Artisan;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Console\ClientCommand;

/**
 * Class StagingController
 * @package App\Http\Controllers
 */
class StagingController extends Controller
{
    /**
     * @var ClientRepository
     */
    private $clientRepository;

    /**
     * StagingController constructor.
     * @param ClientRepository $clientRepository
     */
    public function __construct(ClientRepository $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function get()
    {
        // create new passport client
        $client = $this->clientRepository->createPasswordGrantClient(
            null,
            'Exosuite Website',
            'http://localhost'
        );

        return $this->created([
            'client_id' => $client->id,
            'client_secret' => $client->secret
        ]);
    }
}
