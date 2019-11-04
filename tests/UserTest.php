<?php



use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

require('vendor/autoload.php');

/**
 * Class UserTest.
 *
 * @covers \App\User
 */
class UserTest extends TestCase
{
    /**
     * @var User $user An instance of "User" to test.
     */
    private $user;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::all()->random();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        $this->beforeApplicationDestroyed(function () {
            DB::disconnect();
        });
        $this->user = null;
        parent::tearDown();
    }

    /**
     * @covers \App\User::role
     */
    public function testUsers(): void
    {
        $this->setUp();
        $response = $this->user->with('role')->get();
        $this->assertNotEmpty($response);
        $this->assertInstanceOf('\Illuminate\Database\Eloquent\Collection', $response);
        foreach($response as $entry)
        {
            $this->assertTrue($response->contains('role', $entry->role));
        }
        $this->assertFalse($this->user->hasRole('fakeRole'));
        $this->assertTrue($this->user->hasRole($this->user->getRole()));
        $customClaims = $this->user->getJWTCustomClaims();
        $this->assertTrue(array_key_exists('role', $customClaims));
        $primaryKey = $this->user->getJWTIdentifier();
        $this->assertTrue(is_int($primaryKey));
        $this->tearDown();
    }


}
