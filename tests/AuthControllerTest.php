<?php



use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

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
        parent::setUp();
        $this->authController = new AuthController();
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
        $this->authController = new AuthController();
        $this->client = null;
        parent::tearDown();
    }

    /**
     * @covers \App\Http\Controllers\AuthController::register
     * @dataProvider dataRegisterProvider
     */
    public function testRegister($username, $email, $password, $responseCode): void
    {
        $this->setUp();
        $requestData = [
            'username' => $username,
            'email' => $email,
            'password' => $password
        ];
        $response = $this->sendRequestWithoutAuthorization('POST', '/register', $requestData);
        DB::table('users')->where('email', $email)->delete();
        $this->assertEquals($responseCode, $response->getStatusCode());
        if($response->getStatusCode() == 201 || $response->getStatusCode() == 422)
        {
            $request = new Request();
            $request->setMethod('POST');
            $request->request->add($requestData);
            $test = $this->authController->register($request);
            fwrite(STDERR, $test);
            if($response->getStatusCode() == 201)
                DB::table('users')->where('email', $email)->delete();
        }        
        $this->tearDown();
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

    public function sendRequestWithoutAuthorization($requestType, $url, array $data)
    {
        return $this->client->request($requestType, $url, [
            'query' => $data
        ]); 
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

    public function dataRegisterProvider() {
        return array(
            array('Username1','provider1@test.lt', '123456', 201),
            array('InvalidUsername..','invalid_email', '123456', 422),
            array('Username2','provider2@test.lt', '123456', 201),
            array('InvalidUsername..','invalid_email', '123456', 422),
            array('Username3','provider3@test.lt', '123456', 201),
            array('InvalidUsername..','invalid_email', '123456', 422),
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
