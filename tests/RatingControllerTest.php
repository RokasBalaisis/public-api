<?php


use PHPUnit\Framework\MockObject\MockObject;
use App\Http\Controllers\RatingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Rating;

require('vendor/autoload.php');

/**
 * Class RatingControllerTest.
 *
 * @covers \App\Http\Controllers\RatingController
 */
class RatingControllerTest extends TestCase
{
    /**
     * @var Client $client An instance of Client to test.
     */
    protected $client;

    /**
     * @var RatingController $categoryController An instance of "CategoryController" to test.
     */
    private $ratingController;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->ratingController = new RatingController();
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
        $this->ratingController = null;
        $this->client = null;
        parent::tearDown();
    }

    /**
     * @covers \App\Http\Controllers\RatingController::index
     * @dataProvider dataIndexProvider
     */
    public function testIndex($email, $password, $responseCode): void
    {
        $this->setUp();
        $authorization = $this->authorize($email, $password);
        $requestData = [];
        $response = $this->sendRequest($authorization, 'GET', '/ratings', $requestData);
        $this->assertEquals($responseCode, $response->getStatusCode());
        if($response->getStatusCode() == 200)
            $this->ratingController->index();
        $this->tearDown();
    }

    /**
     * @covers \App\Http\Controllers\RatingController::store
     * @dataProvider dataStoreProvider
     */
    public function testStore($email, $password, $media_id, $user_id, $rating, $responseCode): void
    {
        $this->setUp();
        $authorization = $this->authorize($email, $password);
        $requestData = [
            'media_id' => $media_id,
            'user_id' => $user_id,
            'rating' => $rating,
        ];
        $response = $this->sendRequest($authorization, 'POST', '/ratings', $requestData);
        
        $data = json_decode($response->getBody(), true);
        if(isset($data['rating']))
            Rating::destroy($data['rating']['id']);
        $this->assertEquals($responseCode, $response->getStatusCode());
        if($response->getStatusCode() == 201 || $response->getStatusCode() == 422)
        {
            $request = new Request();
            $request->setMethod('POST');
            $request->request->add($requestData);
            $response = $this->ratingController->store($request);
            if($response->getStatusCode() == 201){
                Rating::destroy(DB::table('ratings')->max('id'));
            }
        }
        $this->tearDown();
    }

    /**
     * @covers \App\Http\Controllers\RatingController::show
     */
    public function testShow(): void
    {
        /** @todo Complete this unit test method. */
        $this->markTestIncomplete();
    }

    /**
     * @covers \App\Http\Controllers\RatingController::update
     */
    public function testUpdate(): void
    {
        /** @todo Complete this unit test method. */
        $this->markTestIncomplete();
    }

    /**
     * @covers \App\Http\Controllers\RatingController::destroy
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
            array('admin@admin.lt', 'admin', '1', '1', '5', 201),
            array('admin@admin.lt', 'admin', '1', '1', '100', 422),
            array('admin@admin.lt', 'admin', '100000', '1000000', '2', 422),
            array('test1@test.lt', '123456', '1', '2', '4', 403),
            array('fake@user.lt', '123456', '1', '2', '4', 401)
        );
    }

    public function dataShowProvider()
    {
        return array(
            array('admin@admin.lt', 'admin', '1', 200),
            array('test1@test.lt', '123456', '1', 200),
            array('fake@user.lt', '123456', '1', 200),
            array('admin@admin.lt', 'admin', '99999', 404),
            array('admin@admin.lt', 'admin', '61346', 404),
            array('fake@user.lt', 'fakepassword', '1', 200)
        );
    }

    public function dataUpdateProvider()
    {
        return array(
            array('admin@admin.lt', 'admin', '1', 'testcategory', '67', 200),
            array('admin@admin.lt', 'admin', '1', 'testcategory', '999999', 404),
            array('admin@admin.lt', 'admin', '1', 'test', '67', 422),
            array('admin@admin.lt', 'admin', '50', 'testcategory', '67', 422),
            array('test1@test.lt', '123456', '1', 'testcategory', '67', 403),
            array('fake@user.lt', '123456', '1', 'testcategory', '67', 401),
            array('administrator@admin.lt', 'fakepassword', '1', 'testcategory', '67', 401)
        );
    }

    public function dataDestroyProvider()
    {
        return array(
            array('admin@admin.lt', 'admin', '9', 200),
            array('admin@admin.lt', 'admin', '1', 422),
            array('test1@test.lt', '123456', '9', 403),
            array('fake@user.lt', '123456', '9', 401),
            array('admin@admin.lt', 'admin', '99999', 404),
            array('admin@admin.lt', 'admin', '61346', 404),
            array('fake@user.lt', 'fakepassword', '9', 401)
        );
    }
}
