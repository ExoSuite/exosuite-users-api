<?php declare(strict_types = 1);

namespace Tests\Feature;

use Illuminate\Http\Response;
use Tests\TestCase;

/**
 * Class HorizonTest
 *
 * @package Tests\Feature
 */
class HorizonTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testViewHorizonDashboard(): void
    {
        $response = $this->get($this->route('horizon.index'));
        $response->assertStatus(Response::HTTP_OK);
    }
}
