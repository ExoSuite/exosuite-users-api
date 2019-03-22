<?php declare(strict_types = 1);

namespace Tests\Unit;

use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Tests\TestCase;

class PreFlightCheckTest extends TestCase
{

    public function testSendEmailIsAlreadyRegisteredRequestWithBadMail(): void
    {
        $response = $this->post($this->route('preflight_is_mail_available'), ['email' => Str::random()]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testSendEmailIsAlreadyRegisteredRequestWithNoParameter(): void
    {
        $response = $this->post($this->route('preflight_is_mail_available'));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
