<?php


use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
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
        /** @todo Maybe add some arguments to this constructor */
        $this->mediaTypeController = new MediaTypeController();
        $this->client = new GuzzleHttp\Client([
            'base_uri' => 'https://api.moviesandtvshows.com/',
            'http_errors' => false
        ]);
    }

    /**
     * @covers \App\Http\Controllers\MediaTypeController::index
     * @dataProvider providerIndexData
     */
    public function testIndex($email, $password, $responseCode): void
    {
        $response = $this->client->post('/login', [
            'query' => [
                'email' => $email,
                'password' => $password
            ]
        ]);
        if(isset($response->getHeaders()['Authorization']))
        {
            $response = $this->client->get('/mediatypes', [
                'headers' => [
                    'Authorization'     => $response->getHeaders()['Authorization']
                ]
            ]);
        }
        else
        {
            $response = $this->client->get('/mediatypes');  
        }
        $this->assertEquals($responseCode, $response->getStatusCode());
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

    public function providerIndexData() {
        return array(
            array('admin@admin.lt', 'admin', 200),
            array('test1@test.lt', '123456', 200),
            array('fake@user.lt', '123456', 200),
            array('administrator@admin.lt', 'fakepassword', 200),
            array('test1@test.lt', 'fakepassword', 200),
            array('fake@user.lt', 'fakepassword', 200)
        );
    }
    public function providerStoreData() {
        return array(
            array('admin@admin.lt', 'admin', 'Testingtype', 201),
            array('test1@test.lt', '123456', 'Testingtype', 403),
            array('fake@user.lt', '123456', 'Testingtype', 401),
            array('administrator@admin.lt', 'fakepassword', 'Testingtype', 401),
            array('test1@test.lt', 'fakepassword', 'Testingtype', 401),
            array('fake@user.lt', 'fakepassword', 'Testingtype', 401)
        );
    }
    public function providerShowData() {
        return array(
            array('admin@admin.lt', 'admin', '1', 200),
            array('test1@test.lt', '123456', '1', 200),
            array('fake@user.lt', '123456', '1', 200),
            array('administrator@admin.lt', 'fakepassword', '8989898', 404),
            array('test1@test.lt', 'fakepassword', '17578678', 404),
            array('fake@user.lt', 'fakepassword', '275827437', 404)
        );
    }
    public function providerUpdateData() {
        return array(
            array('admin@admin.lt', 'admin', Faker::create()->unique()->firstName, '9', 200),
            array('test1@test.lt', '123456', Faker::create()->unique()->firstName, '9', 403),
            array('fake@user.lt', '123456', Faker::create()->unique()->firstName, '9', 401),
            array('administrator@admin.lt', 'fakepassword', Faker::create()->unique()->firstName, '6518', 401),
            array('test1@test.lt', 'fakepassword', Faker::create()->unique()->firstName, '9859484', 401),
            array('fake@user.lt', 'fakepassword', Faker::create()->unique()->firstName, '416421', 401)
        );
    }
    public function providerDestroyData() {
        return array(
            array('admin@admin.lt', 'admin', null, 200),
            array('test1@test.lt', '123456', '2', 403),
            array('fake@user.lt', '123456', '2', 401),
            array('admin@admin.lt', 'admin', '99999', 404),
            array('admin@admin.lt', 'admin', '61346', 404),
            array('fake@user.lt', 'fakepassword', '2', 401)
        );
    }
}
