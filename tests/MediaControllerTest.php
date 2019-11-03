<?php


use PHPUnit\Framework\MockObject\MockObject;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\MediaController;
use Illuminate\Http\Request;
use App\Media;
use App\MediaFile;

require('vendor/autoload.php');

/**
 * Class MediaControllerTest.
 *
 * @covers \App\Http\Controllers\MediaController
 */
class MediaControllerTest extends TestCase
{
   /**
     * @var Client $client An instance of Client to test.
     */
    protected $client;

    /**
     * @var MediaController $mediaController An instance of "MediaController" to test.
     */
    private $mediaController;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->mediaController = new MediaController();
        $this->client = new GuzzleHttp\Client([
            'base_uri' => 'https://api.moviesandtvshows.com/',
            'http_errors' => false
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        $this->beforeApplicationDestroyed(function () {
            DB::disconnect();
        });
        $this->mediaController = null;
        $this->client = null;
        parent::tearDown();
    }

    /**
     * @covers \App\Http\Controllers\MediaController::index
     * @dataProvider dataIndexProvider
     */
    public function testIndex($email, $password, $responseCode): void
    {
        $this->setUp();
        $authorization = $this->authorize($email, $password);
        $requestData = [];
        $response = $this->sendRequest($authorization, 'GET', '/media', $requestData);
        $this->assertEquals($responseCode, $response->getStatusCode());
        if($response->getStatusCode() == 200)
            $this->mediaController->index();
        $this->tearDown();
    }

    /**
     * @covers \App\Http\Controllers\MediaController::store
     * @dataProvider dataStoreProvider
     */
    public function testStore(): void
    {
        /** @todo Complete this unit test method. */
        $this->markTestIncomplete();
    }

    /**
     * @covers \App\Http\Controllers\MediaController::show
     * @dataProvider dataShowProvider
     */
    public function testShow(): void
    {
        /** @todo Complete this unit test method. */
        $this->markTestIncomplete();
    }

    /**
     * @covers \App\Http\Controllers\MediaController::update
     * @dataProvider dataUpdateProvider
     */
    public function testUpdate(): void
    {
        /** @todo Complete this unit test method. */
        $this->markTestIncomplete();
    }

    /**
     * @covers \App\Http\Controllers\MediaController::destroy
     * @dataProvider dataDestroyProvider
     */
    public function testDestroy(): void
    {
        /** @todo Complete this unit test method. */
        $this->markTestIncomplete();
    }

    public function authorize($email, $password)
    {
       return $this->client->post('/login', [
            'query' => [
                'email' => $email,
                'password' => $password
            ]
        ]);
    }

    public function setUserId($email, $password)
    {
        $token = Auth::attempt(['email' => $email, 'password' => $password]);
        if($token == null)
            return null;
        else
            return Auth::user()->id;
    }

    public function sendRequest($authorization, $requestType, $url, array $data)
    {
    if(isset($authorization->getHeaders()['Authorization']))
       {
           return $this->client->request($requestType, $url, [
               'headers' => [
                   'Authorization'     => $authorization->getHeaders()['Authorization']
               ],
               'query' => $data
           ]);
       }
       else
       {
        return $this->client->request($requestType, $url, [
            'query' => $data
        ]); 
       }
    }

    public function dataIndexProvider()
    {
        return array(
            array('admin@admin.lt', 'admin', 200),
            array('test1@test.lt', '123456', 200),
            array('fake@user.lt', '123456', 200),
            array('admin@admin.lt', 'fakepassword', 200),
        );
    }

    public function dataStoreProvider()
    {
        return array(
            array('admin@admin.lt', 'admin', 'testingUserStore', 'testuser@email.lt', '123456', '2', 201),
            array('admin@admin.lt', 'admin', 'testing.UserStore', 'testuser@email.lt', '123456', '2', 422),
            array('test1@test.lt', '123456', 'testingUserStore', 'testuser@email.lt', '123456', '2', 403),
            array('fake@user.lt', '123456', 'testingUserStore', 'testuser@email.lt', '123456', '2', 401)
        );
    }

    public function dataShowProvider()
    {
        return array(
            array('admin@admin.lt', 'admin', '1', 200),
            array('test1@test.lt', '123456', '1', 403),
            array('fake@user.lt', '123456', '1', 401),
            array('admin@admin.lt', 'admin', '99999', 404),
            array('admin@admin.lt', 'admin', '61346', 404),
            array('fake@user.lt', 'fakepassword', '1', 401)
        );
    }

    public function dataUpdateProvider()
    {
        return array(
            array('admin@admin.lt', 'admin', 'testingUser', 'testuserupdated@email.lt', '123456', '2', '15', 200),
            array('admin@admin.lt', 'admin', 'testingUser', 'testuserupdated@email.lt', '123456', '2', '999999', 404),
            array('admin@admin.lt', 'admin', 'testing.User', 'testuserupdated@email.lt', '123456', '2', '15', 422),
            array('test1@test.lt', '123456', 'testingUser', 'testuserupdated@email.lt', '123456', '2', '15', 403),
            array('fake@user.lt', '123456', 'testingUser', 'testuserupdated@email.lt', '123456', '2', '15', 401)
        );
    }

    public function dataDestroyProvider()
    {
        return array(
            array('admin@admin.lt', 'admin', '3', 200),
            array('test1@test.lt', '123456', '3', 403),
            array('fake@user.lt', '123456', '3', 401),
            array('admin@admin.lt', 'admin', '99999', 404),
            array('admin@admin.lt', 'admin', '61346', 404),
            array('fake@user.lt', 'fakepassword', '3', 401)
        );
    }
}
