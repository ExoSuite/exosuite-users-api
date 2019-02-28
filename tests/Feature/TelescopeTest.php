<?php declare(strict_types = 1);

namespace Tests\Feature;

use Illuminate\Http\Response;
use Tests\TestCase;

/**
 * Class TelescopeTest
 *
 * @package Tests\Feature
 */
class TelescopeTest extends TestCase
{

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testViewTelescopeDashboard(): void
    {
        $response = $this->get($this->route('telescope'));
        $response->assertStatus(Response::HTTP_OK);
    }
}
