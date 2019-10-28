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
        $response = $this->client->post('/login', [
            'query' => [
                'email' => $email,
                'password' => $password
            ]
        ]);
        if(isset($response->getHeaders()['Authorization']))
        {
            $response = $this->client->get('/actors', [
                'headers' => [
                    'Authorization'     => $response->getHeaders()['Authorization']
                ]
            ]);
        }
        else
        {
            $response = $this->client->get('/actors');  
        }
        $this->assertEquals($responseCode, $response->getStatusCode());
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
     */
    public function testShow(): void
    {
        /** @todo Complete this unit test method. */
        $this->markTestIncomplete();
    }

    /**
     * @covers \App\Http\Controllers\ActorController::update
     */
    public function testUpdate(): void
    {
        /** @todo Complete this unit test method. */
        $this->markTestIncomplete();
    }

    /**
     * @covers \App\Http\Controllers\ActorController::destroy
     */
    public function testDestroy(): void
    {
        /** @todo Complete this unit test method. */
        $this->markTestIncomplete();
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
}
