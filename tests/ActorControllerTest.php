<?php


use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use App\Http\Controllers\ActorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

require('vendor/autoload.php');

/**
 * Class ActorControllerTest.
 *
 * @covers \App\Http\Controllers\ActorController
 */
class ActorControllerTest extends TestCase
{
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
        /** @todo Maybe add some arguments to this constructor */
        $this->actorController = new ActorController();
        $this->client = new GuzzleHttp\Client([
            'base_uri' => 'https://api.moviesandtvshows.com/',
            'http_errors' => false
        ]);
    }

    /**
     * @covers \App\Http\Controllers\ActorController::index
     * @dataProvider providerIndexData
     */
    public function testIndex($email, $password, $responseCode): void
    {
        // $response = $this->client->post('/login', [
        //     'query' => [
        //         'email' => $email,
        //         'password' => $password
        //     ]
        // ]);
        // if(isset($response->getHeaders()['Authorization']))
        // {
        //     $response = $this->client->get('/actors', [
        //         'headers' => [
        //             'Authorization'     => $response->getHeaders()['Authorization']
        //         ]
        //     ]);
        // }
        // else
        // {
        //     $response = $this->client->get('/actors');  
        // }
        // $this->assertEquals($responseCode, $response->getStatusCode());
        
    }

    /**
     * @covers \App\Http\Controllers\ActorController::store
     * @dataProvider providerStoreData
     */
    public function testStore($email, $password, $name, $surname, $born, $info, $responseCode): void
    {
        $response = $this->client->post('/login', [
            'query' => [
                'email' => $email,
                'password' => $password
            ]
        ]);
        if(isset($response->getHeaders()['Authorization']))
        {
            $response = $this->client->post('/actors', [
                'headers' => [
                    'Authorization'     => $response->getHeaders()['Authorization']
                ],
                'query' => ['name' => $name, 'surname' => $surname, 'born' => $born, 'info' => $info]
            ]);
        }
        else
        {
            $response = $this->client->post('/actors',
             ['query' => ['name' => $name, 'surname' => $surname, 'born' => $born, 'info' => $info]
             ]);  
        }
        $this->assertEquals($responseCode, $response->getStatusCode());
        $data = json_decode($response->getBody(), true);
        if($response->getStatusCode() == 201)
            DB::table('actors')->where('id', $data['actor']['id'])->delete();
    }

    /**
     * @covers \App\Http\Controllers\ActorController::show
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
            $response = $this->client->get(('/actors' . '/' . $id), [
                'headers' => [
                    'Authorization'     => $response->getHeaders()['Authorization']
                ]
            ]);
        }
        else
        {
            $response = $this->client->get(('/actors' . '/' . $id),);  
        }
        $this->assertEquals($responseCode, $response->getStatusCode());
    }
    

    /**
     * @covers \App\Http\Controllers\ActorController::update
     * @dataProvider providerUpdateData
     */
    public function testUpdate($email, $password, $name, $surname, $born, $info, $id, $responseCode): void
    {
        $response = $this->client->post('/login', [
            'query' => [
                'email' => $email,
                'password' => $password
            ]
        ]);
        if(isset($response->getHeaders()['Authorization']))
        {
            $response = $this->client->put('/actors' . '/' . $id, [
                'headers' => [
                    'Authorization'     => $response->getHeaders()['Authorization']
                ],
                'query' => ['name' => $name, 'surname' => $surname, 'born' => $born, 'info' => $info]
            ]);
        }
        else
        {
            $response = $this->client->put('/actors' . '/' . $id,
             ['query' => ['name' => $name, 'surname' => $surname, 'born' => $born, 'info' => $info]
             ]);  
        }
        $this->assertEquals($responseCode, $response->getStatusCode());
        $data = json_decode($response->getBody(), true);
    }

    /**
     * @covers \App\Http\Controllers\ActorController::destroy
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
                DB::table('actors')->insert(['name' => 'Provided', 'surname' => 'Provider', 'born' => '2000-01-01 15:15:15', 'info' => 'Provided info']);
                $id = DB::table('actors')->max('id');
            }
            $response = $this->client->delete(('/actors' . '/' . $id), [
                'headers' => [
                    'Authorization'     => $response->getHeaders()['Authorization']
                ]
            ]);
        }
        else
        {
            $response = $this->client->delete(('/actors' . '/' . $id),);  
        }
        $this->assertEquals($responseCode, $response->getStatusCode());
    }

    public function providerIndexData() {
        return array(
            array('admin@admin.lt', 'admin', 200),
            array('test1@test.lt', '123456', 403),
            array('fake@user.lt', '123456', 401),
            array('administrator@admin.lt', 'fakepassword', 401),
            array('test1@test.lt', 'fakepassword', 401),
            array('fake@user.lt', 'fakepassword', 401)
        );
    }
    public function providerStoreData() {
        return array(
            array('admin@admin.lt', 'admin', 'Providedname', 'Providedsurname', '2000-01-01 12:12:12', 'Providedinfo', 201),
            array('test1@test.lt', '123456', 'Providedname', 'Providedsurname', '2000-01-01 12:12:12', 'Providedinfo', 403),
            array('fake@user.lt', '123456', 'Providedname', 'Providedsurname', '2000-01-01 12:12:12', 'Providedinfo', 401),
            array('administrator@admin.lt', 'fakepassword', 'Providedname', 'Providedsurname', '2000-01-01 12:12:12', 'Providedinfo', 401),
            array('test1@test.lt', 'fakepassword','Providedname', 'Providedsurname', '2000-01-01 12:12:12', 'Providedinfo', 401),
            array('fake@user.lt', 'fakepassword', 'Providedname', 'Providedsurname', '2000-01-01 12:12:12', 'Providedinfo', 401)
        );
    }
    public function providerShowData() {
        return array(
            array('admin@admin.lt', 'admin', '1', 200),
            array('test1@test.lt', '123456', '1', 403),
            array('fake@user.lt', '123456', '1', 401),
            array('admin@admin.lt', 'admin', '99999', 404),
            array('admin@admin.lt', 'admin', '61346', 404),
            array('fake@user.lt', 'fakepassword', '1', 401)
        );
    }
    public function providerUpdateData() {
        return array(
            array('admin@admin.lt', 'admin', 'Providedname', 'Providedsurname', '2000-01-01 12:12:12', 'Providedinfo', '21', 200),
            array('test1@test.lt', '123456', 'Providedname', 'Providedsurname', '2000-01-01 12:12:12', 'Providedinfo', '21', 403),
            array('fake@user.lt', '123456', 'Providedname', 'Providedsurname', '2000-01-01 12:12:12', 'Providedinfo', '21', 401),
            array('administrator@admin.lt', 'fakepassword', 'Providedname', 'Providedsurname', '2000-01-01 12:12:12', 'Providedinfo', '8498', 401),
            array('admin@admin.lt', 'admin', 'Providedname', 'Providedsurname', '2000-01-01 12:12:12', 'Providedinfo', '99999', 404),
            array('fake@user.lt', 'fakepassword', 'Providedname', 'Providedsurname', '2000-01-01 12:12:12', 'Providedinfo', '65149', 401)
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
