<?php


use PHPUnit\Framework\MockObject\MockObject;
use App\Http\Controllers\MediaTypeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

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
    }


    /**
     * @covers \App\Http\Controllers\MediaTypeController::store
     * @dataProvider providerStoreData
     */
    public function testStore($email, $password, $name, $responseCode): void
    {
        $response = $this->client->post('/login', [
            'query' => [
                'email' => $email,
                'password' => $password
            ]
        ]);
        if(isset($response->getHeaders()['Authorization']))
        {
            $response = $this->client->post('/mediatypes', [
                'headers' => [
                    'Authorization'     => $response->getHeaders()['Authorization']
                ],
                'query' => [
                    'name' => $name
                ]
            ]);
        }
        else
        {
            $response = $this->client->post('/mediatypes', [
                'query' => [
                    'name' => $name
            ]]);  
        }
        if($response->getStatusCode() == 201)
            DB::table('media_types')->where('id', DB::table('media_types')->max('id'))->delete();
        $this->assertEquals($responseCode, $response->getStatusCode());
    }

    /**
     * @covers \App\Http\Controllers\MediaTypeController::show
     * @dataProvider providerShowData
     */
    public function testShow($email, $password, $id, $responseCode): void
    {
        $response = $this->client->post('/login', [
            'query' => [
                'email' => $email,
                'password' => $password
            ]
        ]);
        if(isset($response->getHeaders()['Authorization']))
        {
            $response = $this->client->get(('/mediatypes' . '/' . $id), [
                'headers' => [
                    'Authorization'     => $response->getHeaders()['Authorization']
                ]
            ]);
        }
        else
        {
            $response = $this->client->get(('/mediatypes' . '/' . $id),);  
        }
        $this->assertEquals($responseCode, $response->getStatusCode());
    }


    /**
     * @covers \App\Http\Controllers\MediaTypeController::update
     * @dataProvider providerUpdateData
     */
    public function testUpdate($email, $password, $name, $id, $responseCode): void
    {
        $response = $this->client->post('/login', [
            'query' => [
                'email' => $email,
                'password' => $password
            ]
        ]);
        if(isset($response->getHeaders()['Authorization']))
        {
            $response = $this->client->put('/mediatypes' . '/' . $id, [
                'headers' => [
                    'Authorization'     => $response->getHeaders()['Authorization']
                ],
                'query' => [
                    'name' => $name
                ]
            ]);
        }
        else
        {
            $response = $this->client->put('/mediatypes' . '/' . $id, [
                'query' => [
                    'name' => $name
            ]]);  
        }
        $this->assertEquals($responseCode, $response->getStatusCode());
    }

    /**
     * @covers \App\Http\Controllers\MediaTypeController::destroy
     * @dataProvider providerDestroyData
     */
    public function testDestroy($email, $password, $id, $responseCode): void
    {
        $response = $this->client->post('/login', [
            'query' => [
                'email' => $email,
                'password' => $password
            ]
        ]);
        if(isset($response->getHeaders()['Authorization']))
        {
            if($responseCode == 200){
                DB::table('media_types')->insert(['name' => 'Deletable']);
                $id = DB::table('media_types')->max('id');
            }
            $response = $this->client->delete(('/mediatypes' . '/' . $id), [
                'headers' => [
                    'Authorization'     => $response->getHeaders()['Authorization']
                ]
            ]);
        }
        else
        {
            $response = $this->client->delete(('/mediatypes' . '/' . $id),);  
        }
        $this->assertEquals($responseCode, $response->getStatusCode());
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
