<?php


use PHPUnit\Framework\MockObject\MockObject;
use App\Http\Controllers\ActorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Actor;

require('vendor/autoload.php');

/**
 * Class ActorControllerTest.
 *
 * @covers \App\Http\Controllers\ActorController
 */
class ActorControllerTest extends TestCase
{
    /**
     * @var Client $client An instance of Client to test.
     */
    protected $client;

    /**
     * @var ActorController $actorController An instance of "ActorController" to test.
     */
    private $actorController;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->actorController = new ActorController();
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
        $this->mediaTypeController = null;
        $this->client = null;
        parent::tearDown();
    }

    /**
     * @covers \App\Http\Controllers\ActorController::index
     * @dataProvider dataIndexProvider
     */
    public function testIndex($email, $password, $responseCode): void
    {
        $this->setUp();
        $authorization = $this->authorize($email, $password);
        $requestData = [];
        $response = $this->sendRequest($authorization, 'GET', '/actors', $requestData);
        $this->assertEquals($responseCode, $response->getStatusCode());
        if($response->getStatusCode() == 200)
            $this->actorController->index();
        $this->tearDown();
    }

    /**
     * @covers \App\Http\Controllers\ActorController::store
     * @dataProvider dataStoreProvider
     */
    public function testStore($email, $password, $name, $surname, $born, $info, $responseCode): void
    {
        $this->setUp();
        $authorization = $this->authorize($email, $password);
        $requestData = [
            'name' => $name,
            'surname' => $surname,
            'born'  => $born,
            'info' => $info
        ];
        $response = $this->sendRequest($authorization, 'POST', '/actors', $requestData);
        $data = json_decode($response->getBody(), true);
        if(isset($data['actor']))
            Actor::destroy($data['actor']['id']);
        $this->assertEquals($responseCode, $response->getStatusCode());
        if($response->getStatusCode() == 201 || $response->getStatusCode() == 422)
        {
            $request = new Request();
            $request->setMethod('POST');
            $request->request->add($requestData);
            $response = $this->actorController->store($request);
            if($response->getStatusCode() == 201)
                Actor::destroy(DB::table('actors')->max('id'));
        }
        $this->tearDown();
    }
    /**
     * @covers \App\Http\Controllers\ActorController::show
     * @dataProvider dataShowProvider
     */
    public function testShow($email, $password, $id, $responseCode): void
    {
        $this->setUp();
        $authorization = $this->authorize($email, $password);
        $requestData = [];
        $response = $this->sendRequest($authorization, 'GET', '/actors'.'/'.$id, $requestData);
        $this->assertEquals($responseCode, $response->getStatusCode());
        if($response->getStatusCode() == 200 || $response->getStatusCode() == 404)
            $this->actorController->show($id);
        $this->tearDown();
    }
    

    /**
     * @covers \App\Http\Controllers\ActorController::update
     * @dataProvider dataUpdateProvider
     */
    public function testUpdate($email, $password, $name, $surname, $born, $info, $id, $responseCode): void
    {
        $this->setUp();
        $authorization = $this->authorize($email, $password);
        $requestData = [
            'name' => $name,
            'surname' => $surname,
            'born'  => $born,
            'info' => $info
        ];
        $response = $this->sendRequest($authorization, 'PUT', '/actors' . '/' . $id, $requestData);
        $this->assertEquals($responseCode, $response->getStatusCode());
        if($response->getStatusCode() == 200 || $response->getStatusCode() == 422 || $response->getStatusCode() == 404)
        {
            $request = new Request();
            $request->setMethod('PUT');
            $request->request->add($requestData);
            $this->actorController->update($request, $id);
        }
        $this->tearDown();
    }

    /**
     * @covers \App\Http\Controllers\ActorController::destroy
     * @dataProvider dataDestroyProvider
     */
    public function testDestroy($email, $password, $id, $responseCode): void
    {
        $this->setUp();
        $authorization = $this->authorize($email, $password);
        $requestData = [];
        $actor = Actor::find($id);
        $response = $this->sendRequest($authorization, 'DELETE', '/actors' . '/' . $id, $requestData);                
        if($response->getStatusCode() == 200  || $response->getStatusCode() == 404 || $response->getStatusCode() == 422)
        {
            if($response->getStatusCode() == 200)
                DB::table('actors')->insert(['id' => $actor->id, 'name' => $actor->name, 'surname' => $actor->surname, 'born' => $actor->born, 'info' => $actor->info, 'created_at' => $actor->created_at, 'updated_at' => $actor->updated_at]);    
            $request = new Request();
            $request->setMethod('DELETE');
            $response = $this->actorController->destroy($id);   
        }
        $this->assertEquals($responseCode, $response->getStatusCode());
        if($response->getStatusCode() == 200)
            DB::table('actors')->insert(['id' => $actor->id, 'name' => $actor->name, 'surname' => $actor->surname, 'born' => $actor->born, 'info' => $actor->info, 'created_at' => $actor->created_at, 'updated_at' => $actor->updated_at]);
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
            array('admin@admin.lt', 'admin', 'Providedname', 'Providedsurname', '2000-01-01 12:12:12', 'Providedinfo', 201),
            array('admin@admin.lt', 'admin', 'Providedname', 'Providedsurname', 'fakedata', 'Providedinfo', 422),
            array('test1@test.lt', '123456', 'Providedname', 'Providedsurname', '2000-01-01 12:12:12', 'Providedinfo', 403),
            array('fake@user.lt', '123456', 'Providedname', 'Providedsurname', '2000-01-01 12:12:12', 'Providedinfo', 401),
            array('administrator@admin.lt', 'fakepassword', 'Providedname', 'Providedsurname', '2000-01-01 12:12:12', 'Providedinfo', 401),
            array('test1@test.lt', 'fakepassword','Providedname', 'Providedsurname', '2000-01-01 12:12:12', 'Providedinfo', 401),
            array('fake@user.lt', 'fakepassword', 'Providedname', 'Providedsurname', '2000-01-01 12:12:12', 'Providedinfo', 401)
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
            array('admin@admin.lt', 'admin', 'Providedname', 'Providedsurname', '2000-01-01 12:12:12', 'Providedinfo', '21', 200),
            array('test1@test.lt', '123456', 'Providedname', 'Providedsurname', '2000-01-01 12:12:12', 'Providedinfo', '21', 403),
            array('fake@user.lt', '123456', 'Providedname', 'Providedsurname', '2000-01-01 12:12:12', 'Providedinfo', '21', 401),
            array('administrator@admin.lt', 'fakepassword', 'Providedname', 'Providedsurname', '2000-01-01 12:12:12', 'Providedinfo', '8498', 401),
            array('admin@admin.lt', 'admin', 'Providedname', 'Providedsurname', '2000-01-01 12:12:12', 'Providedinfo', '99999', 404),
            array('fake@user.lt', 'fakepassword', 'Providedname', 'Providedsurname', '2000-01-01 12:12:12', 'Providedinfo', '65149', 401),
            array('admin@admin.lt', 'admin', 'Providedname', 'Providedsurname', 'incorrect-datetime', 'Providedinfo', '21', 422),
        );
    }

    public function dataDestroyProvider()
    {
        return array(
            array('admin@admin.lt', 'admin', '9', 200),
            array('admin@admin.lt', 'admin', '15', 422),
            array('test1@test.lt', '123456', '9', 403),
            array('fake@user.lt', '123456', '9', 401),
            array('admin@admin.lt', 'admin', '99999', 404),
            array('admin@admin.lt', 'admin', '61346', 404),
            array('fake@user.lt', 'fakepassword', '9', 401)
        );
    }
}
