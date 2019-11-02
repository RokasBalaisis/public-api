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
        /** @todo Maybe add some arguments to this constructor */
        $this->categoryController = new CategoryController();
    }

    /**
     * @covers \App\Http\Controllers\CategoryController::index
     */
    public function testIndex(): void
    {
        /** @todo Complete this unit test method. */
        $this->markTestIncomplete();
    }

    /**
     * @covers \App\Http\Controllers\CategoryController::store
     */
    public function testStore(): void
    {
        /** @todo Complete this unit test method. */
        $this->markTestIncomplete();
    }

    /**
     * @covers \App\Http\Controllers\CategoryController::show
     */
    public function testShow(): void
    {
        /** @todo Complete this unit test method. */
        $this->markTestIncomplete();
    }

    /**
     * @covers \App\Http\Controllers\CategoryController::update
     */
    public function testUpdate(): void
    {
        /** @todo Complete this unit test method. */
        $this->markTestIncomplete();
    }

    /**
     * @covers \App\Http\Controllers\CategoryController::destroy
     */
    public function testDestroy(): void
    {
        /** @todo Complete this unit test method. */
        $this->markTestIncomplete();
    }
}
