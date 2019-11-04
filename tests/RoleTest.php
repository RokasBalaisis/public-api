<?php



use App\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

require('vendor/autoload.php');

/**
 * Class RoleTest.
 *
 * @covers \App\Role
 */
class RoleTest extends TestCase
{
    /**
     * @var Role $role An instance of "Role" to test.
     */
    private $role;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->role = Role::all()->random();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        $this->beforeApplicationDestroyed(function () {
            DB::disconnect();
        });
        $this->role = null;
        parent::tearDown();
    }

    /**
     * @covers \App\Role::users
     */
    public function testUsers(): void
    {
        $this->setUp();
        $response = $this->role->with('users')->get();
        $this->assertNotEmpty($response);
        $this->assertInstanceOf('\Illuminate\Database\Eloquent\Collection', $response);
        foreach($response as $entry)
        {
            $this->assertTrue($response->contains('users', $entry->users));
        }        
        $this->tearDown();
    }


}
