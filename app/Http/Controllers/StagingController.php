<?php declare(strict_types = 1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Laravel\Passport\ClientRepository;

/**
 * Class StagingController
 *
 * @package App\Http\Controllers
 */
class StagingController extends Controller
{
    /** @var \Laravel\Passport\ClientRepository */
    private $clientRepository;

    /**
     * StagingController constructor.
     *
     * @param \Laravel\Passport\ClientRepository $clientRepository
     */
    public function __construct(ClientRepository $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    public function get(): JsonResponse
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
