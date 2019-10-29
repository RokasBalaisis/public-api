<?php

use PHPUnit\Framework\MockObject\MockObject;
use App\Http\Controllers\CommentController;
use Laravel\Lumen\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Comment;


require('vendor/autoload.php');
/**
 * Class CommentControllerTest.
 *
 * @covers \App\Http\Controllers\CommentController
 */
class CommentControllerTest extends TestCase
{
    /**
     * @var Client $client An instance of Client to test.
     */
    protected $client;

    /**
     * @var CommentController $commentController An instance of "CommentController" to test.
     */
    private $commentController;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->commentController = new CommentController();
        $this->client = new GuzzleHttp\Client([
            'base_uri' => 'https://api.moviesandtvshows.com/',
            'http_errors' => false
        ]);
    }

    /**
     * @covers \App\Http\Controllers\CommentController::index
     * @dataProvider dataIndexProvider
     */
    public function testIndex($email, $password, $responseCode): void
    {
        $this->setUp();
        $authorization = $this->authorize($email, $password);
        if(isset($authorization->getHeaders()['Authorization']))
        {
            $response = $this->client->get(('/comments'), [
                'headers' => [
                    'Authorization'     => $authorization->getHeaders()['Authorization']
                ]
            ]);
        }
        else
        {
            $response = $this->client->get(('/actors'),);  
        }
        $this->assertEquals($responseCode, $response->getStatusCode());
        if($response->getStatusCode() == 200)
            $this->commentController->index();
    }

    /**
     * @covers \App\Http\Controllers\CommentController::store
     * @dataProvider dataStoreProvider
     */
    public function testStore($email, $password, $media_id, $text, $responseCode): void
    {
        $this->setUp();
        $authorization = $this->authorize($email, $password);
        $token = Auth::attempt(['email' => $email, 'password' => $password]);


        if(isset($authorization->getHeaders()['Authorization']))
        {
            $response = $this->client->post(('/comments'), [
                'headers' => [
                    'Authorization'     => $authorization->getHeaders()['Authorization']
                ],
                'query' => [
                    'media_id' => $media_id,
                    'user_id' => Auth::user()->id,
                    'text' => $text,
                ]
            ]);
            $data = json_decode($response->getBody(), true);
            if(isset($data['comment']))
                Comment::destroy($data['comment']['id']);
        }
        else
        {
            $response = $this->client->post(('/actors'),[
                'query' => [
                    'media_id' => $media_id,
                    'user_id' => null,
                    'text' => $text,
                ]
            ]);  
        }
        $this->assertEquals($responseCode, $response->getStatusCode());
        if($response->getStatusCode() == 201)
        {
            $request = new Request();
            $request->setMethod('POST');
            $request->request->add(['media_id' => $media_id, 'user_id' => Auth::user()->id, 'text' => $text]);
            $this->commentController->store($request);
        }
            
    }

    /**
     * @covers \App\Http\Controllers\CommentController::show
     */
    public function testShow(): void
    {
        /** @todo Complete this unit test method. */
        $this->markTestIncomplete();
    }

    /**
     * @covers \App\Http\Controllers\CommentController::update
     */
    public function testUpdate(): void
    {
        /** @todo Complete this unit test method. */
        $this->markTestIncomplete();
    }

    /**
     * @covers \App\Http\Controllers\CommentController::destroy
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
            array('admin@admin.lt', 'admin', 15, "Testing", 200),
            array('admin@admin.lt', 'admin', 9999, "Testing", 422),
            array('test1@test.lt', '123456', 15, "Testing", 403),
            array('fake@user.lt', '123456', 15, "Testing", 401),
            array('admin@admin.lt', 'fakepassword', 15, "Testing", 401),
        );
    }
}
