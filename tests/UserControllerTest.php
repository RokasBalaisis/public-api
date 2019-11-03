<?php

use PHPUnit\Framework\MockObject\MockObject;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use App\User;

/**
 * Class UserControllerTest.
 *
 * @covers \App\Http\Controllers\UserController
 */
class UserControllerTest extends TestCase
{
   /**
     * @var Client $client An instance of Client to test.
     */
    protected $client;

    /**
     * @var UserController $categoryController An instance of "CategoryController" to test.
     */
    private $userController;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->userController = new UserController();
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
        $this->userController = null;
        $this->client = null;
        parent::tearDown();
    }

    /**
     * @covers \App\Http\Controllers\UserController::index
     * @dataProvider dataIndexProvider
     */
    public function testIndex($email, $password, $responseCode): void
    {
        $this->setUp();
        $authorization = $this->authorize($email, $password);
        $requestData = [];
        $response = $this->sendRequest($authorization, 'GET', '/users', $requestData);
        $this->assertEquals($responseCode, $response->getStatusCode());
        if($response->getStatusCode() == 200)
            $this->userController->index();
        $this->tearDown();
    }

    /**
     * @covers \App\Http\Controllers\UserController::store
     * @dataProvider dataStoreProvider
     */
    public function testStore($email, $password, $username, $email_store, $password_store, $role_id, $responseCode): void
    {
        $this->setUp();
        $authorization = $this->authorize($email, $password);
        $requestData = [
            'username' => $username,
            'email' => $email_store,
            'password' => $password_store,
            'role_id' => $role_id
        ];
        $response = $this->sendRequest($authorization, 'POST', '/users', $requestData);
        $data = json_decode($response->getBody(), true);
        if(isset($data['user']))
            if(isset($data['user']['id']))
                User::destroy($data['user']['id']);
        $this->assertEquals($responseCode, $response->getStatusCode());
        if($response->getStatusCode() == 201 || $response->getStatusCode() == 422)
        {
            $request = new Request();
            $request->setMethod('POST');
            $request->request->add($requestData);
            $response = $this->userController->store($request);
            if($response->getStatusCode() == 201){
                User::destroy(DB::table('users')->max('id'));
            }
        }
        $this->tearDown();
    }

    /**
     * @covers \App\Http\Controllers\UserController::show
     * @dataProvider dataShowProvider
     */
    public function testShow(): void
    {
        /** @todo Complete this unit test method. */
        $this->markTestIncomplete();
    }

    /**
     * @covers \App\Http\Controllers\UserController::update
     * @dataProvider dataUpdateProvider
     */
    public function testUpdate(): void
    {
        /** @todo Complete this unit test method. */
        $this->markTestIncomplete();
    }

    /**
     * @covers \App\Http\Controllers\UserController::destroy
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
            array('test1@test.lt', '123456', 403),
            array('fake@user.lt', '123456', 401),
            array('admin@admin.lt', 'fakepassword', 401),
        );
    }

    public function dataStoreProvider()
    {
        return array(
            array('admin@admin.lt', 'admin', 'testingUser', 'testuser@email.lt', '123456', '2', 201),
            array('admin@admin.lt', 'admin', 'testing.User', 'testuser@email.lt', '123456', '2', 422),
            array('test1@test.lt', '123456', 'testingUser', 'testuser@email.lt', '123456', '2', 403),
            array('fake@user.lt', '123456', 'testingUser', 'testuser@email.lt', '123456', '2', 401)
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
            array('admin@admin.lt', 'admin', 'tested', '3', 200),
            array('admin@admin.lt', 'admin', 'user', '3', 422),
            array('admin@admin.lt', 'admin', 'admin', '9999999', 404),
            array('test1@test.lt', '123456', 'test', '3', 403),
            array('fake@user.lt', '123456', 'test', '3', 401)
        );
    }

    public function dataDestroyProvider()
    {
        return array(
            array('admin@admin.lt', 'admin', '3', 200),
            array('admin@admin.lt', 'admin', '2', 422),
            array('test1@test.lt', '123456', '3', 403),
            array('fake@user.lt', '123456', '3', 401),
            array('admin@admin.lt', 'admin', '99999', 404),
            array('admin@admin.lt', 'admin', '61346', 404),
            array('fake@user.lt', 'fakepassword', '3', 401)
        );
    }
}
