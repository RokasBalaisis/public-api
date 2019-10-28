<?php



use App\Http\Controllers\AuthController;
use PHPUnit\Framework\TestCase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

require('vendor/autoload.php');

/**
 * Class AuthControllerTest.
 *
 * @covers \App\Http\Controllers\AuthController
 */
class AuthControllerTest extends TestCase
{
    protected $client;
    /**
     * @var AuthController $authController An instance of "AuthController" to test.
     */
    private $authController;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        /** @todo Maybe add some arguments to this constructor */
        $this->authController = new AuthController();
        $this->client = new GuzzleHttp\Client([
            'base_uri' => 'https://api.moviesandtvshows.com/',
            'http_errors' => false
        ]);
    }

    /**
     * @covers \App\Http\Controllers\AuthController::register
     * @dataProvider providerRegisterData
     */
    public function testRegister($username, $email, $password, $responseCode): void
    {
        $response = $this->client->post('/register', [
            'query' => [
                'username' => $username,
                'email' => $email,
                'password' => $password
            ]
        ]);

        $this->assertEquals($responseCode, $response->getStatusCode());
        if($response->getStatusCode() == 422)
            DB::table('users')->where('email', $email)->delete();
    }

    /**
     * @covers \App\Http\Controllers\AuthController::login
     * @dataProvider providerLoginData
     */
    public function testLogin($email, $password, $responseCode): void
    {
                $response = $this->client->post('/login', [
                    'query' => [
                        'email' => $email,
                        'password' => $password
                    ]
                ]);

                $this->assertEquals($responseCode, $response->getStatusCode());
    }

    /**
     * @covers \App\Http\Controllers\AuthController::logout
     * @dataProvider providerLogoutData
     */
    public function testLogout($email, $password, $responseCode): void
    {
        $response = $this->client->post('/login', [
            'query' => [
                'email' => $email,
                'password' => $password
            ]
        ]);
        if(isset($response->getHeaders()['Authorization']))
        {
            $response = $this->client->post('/logout', [
                'headers' => [
                    'Authorization'     => $response->getHeaders()['Authorization']
                ]
            ]);
        }
        else
        {
            $response = $this->client->post('/logout');  
        }
        $this->assertEquals($responseCode, $response->getStatusCode());


    }

    public function providerRegisterData() {
        return array(
            array('Username1','provider1@test.lt', '123456', 201),
            array('Username1','provider1@test.lt', '123456', 422),
            array('Username2','provider2@test.lt', '123456', 201),
            array('Username2','provider2@test.lt', '123456', 422),
            array('Username3','provider3@test.lt', '123456', 201),
            array('Username3','provider3@test.lt', '123456', 422),
        );
    }
    public function providerLoginData() {
        return array(
            array('administrator@admin.lt', '123456', 200),
            array('test1@test.lt', '123456', 200),
            array('fake@user.lt', '123456', 422),
            array('administrator@admin.lt', 'fakepassword', 422),
            array('test1@test.lt', 'fakepassword', 422),
            array('fake@user.lt', 'fakepassword', 422)
        );
    }
    public function providerLogoutData() {
        return array(
            array('administrator@admin.lt', '123456', 200),
            array('test1@test.lt', '123456', 200),
            array('fake@user.lt', '123456', 401),
            array('administrator@admin.lt', 'fakepassword', 401),
            array('test1@test.lt', 'fakepassword', 401),
            array('fake@user.lt', 'fakepassword', 401)
        );
    }
}
