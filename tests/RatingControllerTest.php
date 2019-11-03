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
     * @var RatingController $ratingController An instance of "RatingController" to test.
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
            if(isset($data['rating']['id']))
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
     * @dataProvider dataShowProvider
     */
    public function testShow($email, $password, $id, $responseCode): void
    {
        $this->setUp();
        $authorization = $this->authorize($email, $password);
        $requestData = [];
        $response = $this->sendRequest($authorization, 'GET', '/ratings'.'/'.$id, $requestData);
        $this->assertEquals($responseCode, $response->getStatusCode());
        if($response->getStatusCode() == 200 || $response->getStatusCode() == 404)
            $this->ratingController->show($id);
        $this->tearDown();
    }

    /**
     * @covers \App\Http\Controllers\RatingController::update
     * @dataProvider dataUpdateProvider
     */
    public function testUpdate($email, $password, $media_id, $user_id, $rating, $id, $responseCode): void
    {
        $this->setUp();
        $currentRating = DB::table('ratings')->where('id', $id)->first();
        $authorization = $this->authorize($email, $password);
        $requestData = [
            'media_id' => $media_id,
            'user_id' => $user_id,
            'rating' => $rating,
        ];
        $response = $this->sendRequest($authorization, 'PUT', '/ratings' . '/' . $id, $requestData);
        $this->assertEquals($responseCode, $response->getStatusCode());
        if($response->getStatusCode() != 404)
            Rating::where('id', $id)->update(['media_id' => $currentRating->media_id, 'user_id' => $currentRating->user_id,  'rating' => $currentRating->rating]);
        if($response->getStatusCode() == 200 || $response->getStatusCode() == 422 || $response->getStatusCode() == 404)
        {
            
            $request = new Request();
            $request->setMethod('PUT');
            $request->request->add($requestData);
            $this->ratingController->update($request, $id);
            if($response->getStatusCode() != 404)
                Rating::where('id', $id)->update(['media_id' => $currentRating->media_id, 'user_id' => $currentRating->user_id,  'rating' => $currentRating->rating]);
        }
        $this->tearDown(); 
    }

    /**
     * @covers \App\Http\Controllers\RatingController::destroy
     * @dataProvider dataDestroyProvider
     */
    public function testDestroy($email, $password, $id, $responseCode): void
    {
        $this->setUp();
        $authorization = $this->authorize($email, $password);
        $requestData = [];
        $rating = Rating::find($id);
        $response = $this->sendRequest($authorization, 'DELETE', '/ratings' . '/' . $id, $requestData);                
        if($response->getStatusCode() == 200  || $response->getStatusCode() == 404 || $response->getStatusCode() == 422)
        {
            if($response->getStatusCode() == 200)
                DB::table('ratings')->insert(['id' => $rating->id, 'media_id' => $rating->media_id, 'user_id' => $rating->user_id,  'rating' => $rating->rating,  'created_at' => $rating->created_at, 'updated_at' => $rating->updated_at]);    
            $request = new Request();
            $request->setMethod('DELETE');
            $response = $this->ratingController->destroy($id);
        }
        $this->assertEquals($responseCode, $response->getStatusCode());
        if($response->getStatusCode() == 200)
            DB::table('ratings')->insert(['id' => $rating->id, 'media_id' => $rating->media_id, 'user_id' => $rating->user_id,  'rating' => $rating->rating,  'created_at' => $rating->created_at, 'updated_at' => $rating->updated_at]);
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
            array('admin@admin.lt', 'admin', '1', '1', '5', 201),
            array('admin@admin.lt', 'admin', '1', '1', '100', 422),
            array('admin@admin.lt', 'admin', '100000', '1000000', '2', 422),
            array('test1@test.lt', '123456', '1', '2', '4', 201),
            array('fake@user.lt', '123456', '1', '2', '4', 401)
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
            array('admin@admin.lt', 'admin', '1', '1', '5', '1', 200),
            array('admin@admin.lt', 'admin', '1', '1', '100', '1', 422),
            array('admin@admin.lt', 'admin', '1', '1', '100', '9999999', 404),
            array('admin@admin.lt', 'admin', '100000', '1000000', '2', '1', 422),
            array('test1@test.lt', '123456', '1', '2', '4', '1', 403),
            array('fake@user.lt', '123456', '1', '2', '4', '1', 401)
        );
    }

    public function dataDestroyProvider()
    {
        return array(
            array('admin@admin.lt', 'admin', '9', 200),
            array('test1@test.lt', '123456', '9', 403),
            array('fake@user.lt', '123456', '9', 401),
            array('admin@admin.lt', 'admin', '99999', 404),
            array('admin@admin.lt', 'admin', '61346', 404),
            array('fake@user.lt', 'fakepassword', '9', 401)
        );
    }
}
