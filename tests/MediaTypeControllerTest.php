<?php


use PHPUnit\Framework\MockObject\MockObject;
use App\Http\Controllers\MediaTypeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use App\MediaType;

require('vendor/autoload.php');

/**
 * Class MediaTypeControllerTest.
 *
 * @covers \App\Http\Controllers\MediaTypeController
 */
class MediaTypeControllerTest extends TestCase
{
    protected $client;
    /**
     * @var MediaTypeController $mediaTypeController An instance of "MediaTypeController" to test.
     */
    private $mediaTypeController;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->mediaTypeController = new MediaTypeController();
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
     * @covers \App\Http\Controllers\MediaTypeController::index
     * @dataProvider dataIndexProvider
     */
    public function testIndex($email, $password, $responseCode): void
    {
        $this->setUp();
        $authorization = $this->authorize($email, $password);
        $requestData = [];
        $response = $this->sendRequest($authorization, 'GET', '/mediatypes', $requestData);
        $this->assertEquals($responseCode, $response->getStatusCode());
        if($response->getStatusCode() == 200)
            $this->mediaTypeController->index();
        $this->tearDown();
    }

    /**
     * @covers \App\Http\Controllers\MediaTypeController::indexMedia
     * @dataProvider dataIndexProvider
     */
    public function testIndexMedia($email, $password, $responseCode): void
    {
        $this->setUp();
        $authorization = $this->authorize($email, $password);
        $requestData = [];
        $response = $this->sendRequest($authorization, 'GET', '/mediatypes/media', $requestData);
        $this->assertEquals($responseCode, $response->getStatusCode());
        if($response->getStatusCode() == 200)
            $this->mediaTypeController->indexMedia();
        $this->tearDown();
    }

    /**
     * @covers \App\Http\Controllers\MediaTypeController::store
     * @dataProvider dataStoreProvider
     */
    public function testStore($email, $password, $name, $responseCode): void
    {
        $this->setUp();
        $authorization = $this->authorize($email, $password);
        $requestData = [
            'name' => $name
        ];
        $response = $this->sendRequest($authorization, 'POST', '/mediatypes', $requestData);
        $data = json_decode($response->getBody(), true);
        if(isset($data['media_type']))
            MediaType::destroy($data['media_type']['id']);
        $this->assertEquals($responseCode, $response->getStatusCode());
        if($response->getStatusCode() == 201 || $response->getStatusCode() == 422)
        {
            $request = new Request();
            $request->setMethod('POST');
            $request->request->add($requestData);
            $this->mediaTypeController->store($request);
            if($response->getStatusCode() == 201)
                MediaType::destroy(DB::table('media_types')->max('id'));
        }
        $this->tearDown();
    }

    /**
     * @covers \App\Http\Controllers\MediaTypeController::show
     * @dataProvider dataShowProvider
     */
    public function testShow($email, $password, $id, $responseCode): void
    {
        $this->setUp();
        $authorization = $this->authorize($email, $password);
        $requestData = [];
        $response = $this->sendRequest($authorization, 'GET', '/mediatypes'.'/'.$id, $requestData);
        $this->assertEquals($responseCode, $response->getStatusCode());
        if($response->getStatusCode() == 200 || $response->getStatusCode() == 404)
            $this->mediaTypeController->show($id);
        $this->tearDown();
    }

    /**
     * @covers \App\Http\Controllers\MediaTypeController::showMedia
     * @dataProvider dataShowProvider
     */
    public function testShowMedia($email, $password, $id, $responseCode): void
    {
        $this->setUp();
        $authorization = $this->authorize($email, $password);
        $requestData = [];
        $response = $this->sendRequest($authorization, 'GET', '/mediatypes'.'/'.$id.'/media', $requestData);
        $this->assertEquals($responseCode, $response->getStatusCode());
        if($response->getStatusCode() == 200 || $response->getStatusCode() == 404)
            $this->mediaTypeController->showMedia($id);
        $this->tearDown();
    }


    /**
     * @covers \App\Http\Controllers\MediaTypeController::update
     * @dataProvider dataUpdateProvider
     */
    public function testUpdate($email, $password, $name, $id, $responseCode): void
    {
        $this->setUp();
        $currentName = DB::table('media_types')->where('id', $id)->value('name');
        $authorization = $this->authorize($email, $password);
        $requestData = [
            'name' => $name,
        ];
        $response = $this->sendRequest($authorization, 'PUT', '/mediatypes' . '/' . $id, $requestData);
        $this->assertEquals($responseCode, $response->getStatusCode());
        MediaType::where('id', $id)->update(['name' => $currentName]);
        if($response->getStatusCode() == 200 || $response->getStatusCode() == 422 || $response->getStatusCode() == 404)
        {
            
            $request = new Request();
            $request->setMethod('PUT');
            $request->request->add($requestData);
            $test = $this->mediaTypeController->update($request, $id);
            MediaType::where('id', $id)->update(['name' => $currentName]);
        }
        $this->tearDown(); 
    }

    /**
     * @covers \App\Http\Controllers\MediaTypeController::destroy
     * @dataProvider dataDestroyProvider
     */
    public function testDestroy($email, $password, $id, $responseCode): void
    {
        $this->setUp();
        $authorization = $this->authorize($email, $password);
        $requestData = [];
        $media_type = MediaType::find($id);
        $response = $this->sendRequest($authorization, 'DELETE', '/mediatypes' . '/' . $id, $requestData);         
        if($response->getStatusCode() == 200  || $response->getStatusCode() == 404 || $response->getStatusCode() == 422)
        {   
            if($response->getStatusCode() == 200)
            DB::table('media_types')->insert(['id' => $media_type->id, 'name' => $media_type->name, 'created_at' => $media_type->created_at, 'updated_at' => $media_type->updated_at]); 
            $request = new Request();
            $request->setMethod('DELETE');
            $response = $this->mediaTypeController->destroy($id); 
            if($response->getStatusCode() == 200)
            DB::table('media_types')->insert(['id' => $media_type->id, 'name' => $media_type->name, 'created_at' => $media_type->created_at, 'updated_at' => $media_type->updated_at]);     
        }
        $this->assertEquals($responseCode, $response->getStatusCode());
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
            array('test1@test.lt', '123456', 200),
            array('fake@user.lt', '123456', 200),
            array('administrator@admin.lt', 'fakepassword', 200),
            array('test1@test.lt', 'fakepassword', 200),
            array('fake@user.lt', 'fakepassword', 200)
        );
    }

    public function dataStoreProvider()
    {
        return array(
            array('admin@admin.lt', 'admin', 'Testingtype', 201),
            array('admin@admin.lt', 'admin', 'IvalidInput1652', 422),
            array('test1@test.lt', '123456', 'Testingtype', 403),
            array('fake@user.lt', '123456', 'Testingtype', 401),
            array('administrator@admin.lt', 'fakepassword', 'Testingtype', 401),
            array('test1@test.lt', 'fakepassword', 'Testingtype', 401),
            array('fake@user.lt', 'fakepassword', 'Testingtype', 401)
        );
    }

    public function dataShowProvider()
    {
        return array(
            array('admin@admin.lt', 'admin', '1', 200),
            array('test1@test.lt', '123456', '1', 200),
            array('fake@user.lt', '123456', '1', 200),
            array('administrator@admin.lt', 'fakepassword', '8989898', 404),
            array('test1@test.lt', 'fakepassword', '17578678', 404),
            array('fake@user.lt', 'fakepassword', '275827437', 404)
        );
    }

    public function dataUpdateProvider()
    {
        return array(
            array('admin@admin.lt', 'admin', 'Test', '3', 200),
            array('admin@admin.lt', 'admin', 'movies', '1', 422),
            array('admin@admin.lt', 'admin', 'Test', '9548', 404),
            array('test1@test.lt', '123456', 'Test', '3', 403),
            array('fake@user.lt', '123456', 'Test', '3', 401),
            array('administrator@admin.lt', 'fakepassword', 'Test', '6518', 401),
            array('test1@test.lt', 'fakepassword', 'Test', '9859484', 401),
            array('fake@user.lt', 'fakepassword', 'Test', '416421', 401)
        );
    }

    public function dataDestroyProvider()
    {
        return array(
            array('admin@admin.lt', 'admin', '3', 200),
            array('admin@admin.lt', 'admin', '2', 422),
            array('test1@test.lt', '123456', '2', 403),
            array('fake@user.lt', '123456', '2', 401),
            array('admin@admin.lt', 'admin', '99999', 404),
            array('admin@admin.lt', 'admin', '61346', 404),
            array('fake@user.lt', 'fakepassword', '2', 401)
        );
    }
}
