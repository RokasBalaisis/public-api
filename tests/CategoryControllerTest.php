<?php


use PHPUnit\Framework\MockObject\MockObject;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\CategoryController;
use Illuminate\Http\Request;
use App\Category;

require('vendor/autoload.php');

/**
 * Class CategoryControllerTest.
 *
 * @covers \App\Http\Controllers\CategoryController
 */
class CategoryControllerTest extends TestCase
{
    /**
     * @var Client $client An instance of Client to test.
     */
    protected $client;

    /**
     * @var CategoryController $categoryController An instance of "CategoryController" to test.
     */
    private $categoryController;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->categoryController = new CategoryController();
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
        $this->categoryController = null;
        $this->client = null;
        parent::tearDown();
    }

    /**
     * @covers \App\Http\Controllers\CategoryController::index
     * @dataProvider dataIndexProvider
     */
    public function testIndex($email, $password, $responseCode): void
    {
        $this->setUp();
        $authorization = $this->authorize($email, $password);
        $requestData = [];
        $response = $this->sendRequest($authorization, 'GET', '/categories', $requestData);
        $this->assertEquals($responseCode, $response->getStatusCode());
        if($response->getStatusCode() == 200)
            $this->categoryController->index();
        $this->tearDown();
    }

    /**
     * @covers \App\Http\Controllers\CategoryController::store
     * @dataProvider dataStoreProvider
     */
    public function testStore($email, $password, $media_type_id, $name, $responseCode): void
    {
        $this->setUp();
        $authorization = $this->authorize($email, $password);
        $requestData = [
            'media_type_id' => $media_type_id,
            'name' => $name,
        ];
        $response = $this->sendRequest($authorization, 'POST', '/categories', $requestData);
        $data = json_decode($response->getBody(), true);
        if(isset($data['category']))
            Category::destroy($data['category']['id']);
        $this->assertEquals($responseCode, $response->getStatusCode());
        if($response->getStatusCode() == 201 || $response->getStatusCode() == 422)
        {
            $request = new Request();
            $request->setMethod('POST');
            $request->request->add($requestData);
            $this->categoryController->store($request);
            Category::destroy(DB::table('categories')->max('id'));
        }
        $this->tearDown();
    }

    /**
     * @covers \App\Http\Controllers\CategoryController::show
     * @dataProvider dataShowProvider
     */
    public function testShow($email, $password, $id, $responseCode): void
    {
        $this->setUp();
        $authorization = $this->authorize($email, $password);
        $requestData = [];
        $response = $this->sendRequest($authorization, 'GET', '/categories'.'/'.$id, $requestData);
        $this->assertEquals($responseCode, $response->getStatusCode());
        if($response->getStatusCode() == 200 || $response->getStatusCode() == 404)
            $this->categoryController->show($id);
        $this->tearDown();
    }

    /**
     * @covers \App\Http\Controllers\CategoryController::update
     * @dataProvider dataUpdateProvider
     */
    public function testUpdate($email, $password, $media_type_id, $name, $id, $responseCode): void
    {
        $this->setUp();
        $currentMediaTypeId = DB::table('categories')->where('id', $id)->value('media_type_id');
        $currentName = DB::table('categories')->where('id', $id)->value('name');
        $authorization = $this->authorize($email, $password);
        $requestData = [
            'media_type_id' => $media_type_id,
            'name' => $name
        ];
        $response = $this->sendRequest($authorization, 'PUT', '/categories' . '/' . $id, $requestData);
        $this->assertEquals($responseCode, $response->getStatusCode());
        Category::where('id', $id)->update(['id' => $id, 'media_type_id' => $currentMediaTypeId, 'name' => $currentName]);
        if($response->getStatusCode() == 200 || $response->getStatusCode() == 422 || $response->getStatusCode() == 404)
        {
            
            $request = new Request();
            $request->setMethod('PUT');
            $request->request->add($requestData);
            $this->categoryController->update($request, $id);
            Category::where('id', $id)->update(['id' => $id, 'media_type_id' => $currentMediaTypeId, 'name' => $currentName]);
        }
        $this->tearDown(); 
    }

    /**
     * @covers \App\Http\Controllers\CategoryController::destroy
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
            array('test1@test.lt', '123456', 200),
            array('fake@user.lt', '123456', 200),
            array('admin@admin.lt', 'fakepassword', 200),
        );
    }

    public function dataStoreProvider()
    {
        return array(
            array('admin@admin.lt', 'admin', '1', 'testcategory', 201),
            array('admin@admin.lt', 'admin', '1', 'action', 422),
            array('admin@admin.lt', 'admin', '50', 'testcategory', 422),
            array('test1@test.lt', '123456', '1', 'testcategory', 403),
            array('fake@user.lt', '123456', '1', 'testcategory', 401),
            array('administrator@admin.lt', 'fakepassword', '1', 'testcategory', 401)
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
            array('admin@admin.lt', 'admin', '1', 'testcategory', '58', 200),
            array('admin@admin.lt', 'admin', '1', 'test', '58', 422),
            array('admin@admin.lt', 'admin', '50', 'testcategory', '58', 422),
            array('test1@test.lt', '123456', '1', 'testcategory', '58', 403),
            array('fake@user.lt', '123456', '1', 'testcategory', '58', 401),
            array('administrator@admin.lt', 'fakepassword', '1', 'testcategory', '58', 401)
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
