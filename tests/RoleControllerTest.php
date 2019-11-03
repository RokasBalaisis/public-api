<?php


use PHPUnit\Framework\MockObject\MockObject;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\RoleController;
use Illuminate\Http\Request;
use App\Role;

require('vendor/autoload.php');

/**
 * Class RoleControllerTest.
 *
 * @covers \App\Http\Controllers\RoleController
 */
class RoleControllerTest extends TestCase
{
    /**
     * @var Client $client An instance of Client to test.
     */
    protected $client;

    /**
     * @var RoleController $roleController An instance of "RoleController" to test.
     */
    private $roleController;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->roleController = new RoleController();
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
        $this->roleController = null;
        $this->client = null;
        parent::tearDown();
    }

    /**
     * @covers \App\Http\Controllers\RoleController::index
     * @dataProvider dataIndexProvider
     */
    public function testIndex($email, $password, $responseCode): void
    {
        $this->setUp();
        $authorization = $this->authorize($email, $password);
        $requestData = [];
        $response = $this->sendRequest($authorization, 'GET', '/roles', $requestData);
        $this->assertEquals($responseCode, $response->getStatusCode());
        if($response->getStatusCode() == 200)
            $this->roleController->index();
        $this->tearDown();
    }

    /**
     * @covers \App\Http\Controllers\RoleController::store
     * @dataProvider dataStoreProvider
     */
    public function testStore($email, $password, $name, $responseCode): void
    {
        $this->setUp();
        $authorization = $this->authorize($email, $password);
        $requestData = [
            'name' => $name
        ];
        $response = $this->sendRequest($authorization, 'POST', '/roles', $requestData);
        $data = json_decode($response->getBody(), true);
        if(isset($data['role']))
            if(isset($data['role']['id']))
                Role::destroy($data['role']['id']);
        $this->assertEquals($responseCode, $response->getStatusCode());
        if($response->getStatusCode() == 201 || $response->getStatusCode() == 422)
        {
            $request = new Request();
            $request->setMethod('POST');
            $request->request->add($requestData);
            $response = $this->roleController->store($request);
            if($response->getStatusCode() == 201){
                Role::destroy(DB::table('roles')->max('id'));
            }
        }
        $this->tearDown();
    }

    /**
     * @covers \App\Http\Controllers\RoleController::show
     * @dataProvider dataShowProvider
     */
    public function testShow($email, $password, $id, $responseCode): void
    {
        $this->setUp();
        $authorization = $this->authorize($email, $password);
        $requestData = [];
        $response = $this->sendRequest($authorization, 'GET', '/roles'.'/'.$id, $requestData);
        $this->assertEquals($responseCode, $response->getStatusCode());
        if($response->getStatusCode() == 200 || $response->getStatusCode() == 404)
            $this->roleController->show($id);
        $this->tearDown();
    }

    /**
     * @covers \App\Http\Controllers\RoleController::update
     * @dataProvider dataUpdateProvider
     */
    public function testUpdate($email, $password, $name, $id, $responseCode): void
    {
        $this->setUp();
        $currentRole = DB::table('roles')->where('id', $id)->first();
        $authorization = $this->authorize($email, $password);
        $requestData = [
            'name' => $name
        ];
        $response = $this->sendRequest($authorization, 'PUT', '/roles' . '/' . $id, $requestData);
        $this->assertEquals($responseCode, $response->getStatusCode());
        if($response->getStatusCode() != 404)
            Role::where('id', $id)->update(['name' => $currentRole->name]);
        if($response->getStatusCode() == 200 || $response->getStatusCode() == 422 || $response->getStatusCode() == 404)
        {           
            $request = new Request();
            $request->setMethod('PUT');
            $request->request->add($requestData);
            $this->roleController->update($request, $id);
            if($response->getStatusCode() != 404)
                Role::where('id', $id)->update(['name' => $currentRole->name]);
        }
        $this->tearDown(); 
    }

    /**
     * @covers \App\Http\Controllers\RoleController::destroy
     * @dataProvider dataDestroyProvider
     */
    public function testDestroy($email, $password, $id, $responseCode): void
    {
        $this->setUp();
        $authorization = $this->authorize($email, $password);
        $requestData = [];
        $role = Role::find($id);
        $response = $this->sendRequest($authorization, 'DELETE', '/roles' . '/' . $id, $requestData);                
        if($response->getStatusCode() == 200  || $response->getStatusCode() == 404 || $response->getStatusCode() == 422)
        {
            if($response->getStatusCode() == 200)
                DB::table('roles')->insert(['id' => $role->id, 'name' => $role->name, 'created_at' => $role->created_at, 'updated_at' => $role->updated_at]);    
            $request = new Request();
            $request->setMethod('DELETE');
            $response = $this->roleController->destroy($id);
        }
        $this->assertEquals($responseCode, $response->getStatusCode());
        if($response->getStatusCode() == 200)
            DB::table('roles')->insert(['id' => $role->id, 'name' => $role->name, 'created_at' => $role->created_at, 'updated_at' => $role->updated_at]);  
        $this->tearDown();  
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
            array('admin@admin.lt', 'admin', 'testing', 201),
            array('admin@admin.lt', 'admin', 'admin', 422),
            array('test1@test.lt', '123456', 'testing', 403),
            array('fake@user.lt', '123456', 'testing', 401)
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
