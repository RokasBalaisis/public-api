<?php



use PHPUnit\Framework\MockObject\MockObject;
use App\Http\Controllers\CommentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use App\User;
use Tymon\JWTAuth\Facades\JWTAuth as TymonJWTAuth;

require('vendor/autoload.php');
/**
 * Class CommentControllerTest.
 *
 * @covers \App\Http\Controllers\CommentController
 */
class CommentControllerTest extends TestCase
{
    /**
     * @var User $user An instance of "User" to test.
     */
    private $user;
    /**
     * @var CommentController $commentController An instance of "CommentController" to test.
     */
    private $commentController;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->commentController = new CommentController();
    }

    /**
     * @covers \App\Http\Controllers\CommentController::index
     * @dataProvider dataIndexProvider
     */
    public function testIndex($email, $password, $responseCode): void
    {
        $this->setUp();
        //Auth::attempt(['email' => '$email', 'password' => '$password']);
        //$this->assertNotNull(Auth::user());
        //$userRole= User::with('role')->where('id', Auth::user()->getAuthIdentifier())->first()->role->pluck('name');
        //$this->assertEquals("admin", $userRole[0]);
        $response = $this->commentController->index();
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @covers \App\Http\Controllers\CommentController::store
     */
    public function testStore(): void
    {
        /** @todo Complete this unit test method. */
        $this->markTestIncomplete();
    }

    /**
     * @covers \App\Http\Controllers\CommentController::show
     */
    public function testShow(): void
    {
        /** @todo Complete this unit test method. */
        $this->markTestIncomplete();
    }

    /**
     * @covers \App\Http\Controllers\CommentController::update
     */
    public function testUpdate(): void
    {
        /** @todo Complete this unit test method. */
        $this->markTestIncomplete();
    }

    /**
     * @covers \App\Http\Controllers\CommentController::destroy
     */
    public function testDestroy(): void
    {
        /** @todo Complete this unit test method. */
        $this->markTestIncomplete();
    }

    public function dataIndexProvider()
    {
        return array(
            array('admin@admin.lt', 'admin', 200),
            array('test1@test.lt', '123456', 200)
        );
            

    }
}
