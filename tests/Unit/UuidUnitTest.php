<?php declare(strict_types = 1);

namespace Tests\Unit;


use App\Models\User;
use Laravel\Passport\Passport;
use Tests\TestCase;

/**
 * Class UuidUnitTest
 *
 * @package Tests\Unit
 */
class UuidUnitTest extends TestCase
{

    /** @var \App\Models\User|\Illuminate\Database\Eloquent\Model|null */
    private static $user = null;

    public function testInvalidUuidMustThrow422(): void
    {
        Passport::actingAs(self::$user);

        $response = $this->get($this->route("get_user_profile"), ['user' => 42]);
        dd($response->decodeResponseJson());
    }

    protected function setUp(): void
    {
        if (self::$user) {
            return;
        }

        self::$user = User::create();
    }
}
