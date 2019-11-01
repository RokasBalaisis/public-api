<?php

use PHPUnit\Framework\MockObject\MockObject;
use App\Http\Controllers\CommentController;
use Laravel\Lumen\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Comment;
use Illuminate\Support\Facades\DB;

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
        $requestData = [];
        $response = $this->sendRequest($authorization, 'GET', '/comments', $requestData);
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
        $requestData = [
            'media_id' => $media_id,
            'user_id' => $this->setUserId($email, $password),
            'text'  => $text
        ];
        $response = $this->sendRequest($authorization, 'POST', '/comments', $requestData);
        $data = json_decode($response->getBody(), true);
        if(isset($data['comment']))
            Comment::destroy($data['comment']['id']);
        $this->assertEquals($responseCode, $response->getStatusCode());
        if($response->getStatusCode() == 201 || $response->getStatusCode() == 422)
        {
            $request = new Request();
            $request->setMethod('POST');
            $request->request->add($requestData);
            $this->commentController->store($request);
            Comment::destroy(DB::table('comments')->max('id'));
        }
            
    }

    /**
     * @covers \App\Http\Controllers\CommentController::show
     * @dataProvider dataShowProvider
     */
    public function testShow($email, $password, $id, $responseCode): void
    {
        $this->setUp();
        $authorization = $this->authorize($email, $password);
        $requestData = [];
        $response = $this->sendRequest($authorization, 'GET', '/comments'.'/'.$id, $requestData);
        $this->assertEquals($responseCode, $response->getStatusCode());
        if($response->getStatusCode() == 200 || $response->getStatusCode() == 404)
            $this->commentController->show($id);
    }

    /**
     * @covers \App\Http\Controllers\CommentController::update
     * @dataProvider dataUpdateProvider
     */
    public function testUpdate($email, $password, $media_id, $text, $id, $responseCode): void
    {
        $this->setUp();
        $authorization = $this->authorize($email, $password);
        $requestData = [
            'media_id' => $media_id,
            'user_id' => $this->setUserId($email, $password),
            'text'  => $text
        ];
        $response = $this->sendRequest($authorization, 'PUT', '/comments' . '/' . $id, $requestData);
        $this->assertEquals($responseCode, $response->getStatusCode());
        if($response->getStatusCode() == 200 || $response->getStatusCode() == 422 || $response->getStatusCode() == 404)
        {
            $request = new Request();
            $request->setMethod('PUT');
            $request->request->add($requestData);
            $this->commentController->update($request, $id);
        }
            
    }

    /**
     * @covers \App\Http\Controllers\CommentController::destroy
     * @dataProvider dataDestroyProvider
     */
    public function testDestroy($email, $password, $id, $responseCode): void
    {
        $this->setUp();
        $authorization = $this->authorize($email, $password);
        $requestData = [];
        $comment = Comment::find($id);
        $response = $this->sendRequest($authorization, 'DELETE', '/comments' . '/' . $id, $requestData);     
        fwrite(STDERR, $response->getStatusCode());          
        if($response->getStatusCode() == 200  || $response->getStatusCode() == 404)
        {
            $request = new Request();
            $request->setMethod('DELETE');
            $request->request->add($requestData);
            $response = $this->commentController->destroy($request, $id);   
  
        }
        $this->assertEquals($responseCode, $response->getStatusCode());
        if($comment != null && $response->getStatusCode() == 200)
            DB::table('comments')->insert(['id' => $comment->id, 'media_id' => $comment->media_id, 'user_id' => $comment->user_id, 'text' => $comment->text, 'created_at' => $comment->created_at, 'updated_at' => $comment->updated_at]);    
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
            array('admin@admin.lt', 'admin', 15, "Testing", 201),
            array('admin@admin.lt', 'admin', 9999, "Testing", 422),
            array('test1@test.lt', '123456', 15, "Testing", 201),
            array('fake@user.lt', '123456', 15, "Testing", 401),
            array('admin@admin.lt', 'fakepassword', 15, "Testing", 401),
        );
    }

    public function dataShowProvider()
    {
        return array(
            array('admin@admin.lt', 'admin', 44, 200),
            array('admin@admin.lt', 'admin', 9999, 404),
            array('test1@test.lt', '123456', 2, 403),
            array('fake@user.lt', '123456', 174172572, 401),
            array('admin@admin.lt', 'fakepassword', 1, 401),
        );
    }

    public function dataUpdateProvider()
    {
        return array(
            array('admin@admin.lt', 'admin', 15, "TestingUpdated", 6, 200),
            array('admin@admin.lt', 'admin', 9999, "TestingUpdated", 6, 422),
            array('admin@admin.lt', 'admin', 15, "TestingUpdated", 99999, 404),
            array('test1@test.lt', '123456', 15, "TestingUpdated", 6, 403),
            array('fake@user.lt', '123456', 15, "TestingUpdated", 6, 401),
            array('admin@admin.lt', 'fakepassword', 15, "TestingUpdated", 6, 401),
        );
    }

    public function dataDestroyProvider()
    {
        return array(
            array('admin@admin.lt', 'admin', 7, 200),
            array('admin@admin.lt', 'admin', 9999, 404),
            array('test1@test.lt', '123456', 7, 403),
            array('fake@user.lt', '123456', 174172572, 401),
            array('admin@admin.lt', 'fakepassword', 7, 401),
        );
    }
}
