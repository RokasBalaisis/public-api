<?php

namespace Test\App\Http\Controllers;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;

/**
 * Class UserControllerTest.
 *
 * @covers \App\Http\Controllers\UserController
 */
class UserControllerTest extends TestCase
{
    /**
     * @var UserController $userController An instance of "UserController" to test.
     */
    private $userController;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        /** @todo Maybe add some arguments to this constructor */
        $this->userController = new UserController();
    }

    /**
     * @covers \App\Http\Controllers\UserController::index
     */
    public function testIndex(): void
    {
        /** @todo Complete this unit test method. */
        $this->markTestIncomplete();
    }

    /**
     * @covers \App\Http\Controllers\UserController::store
     */
    public function testStore(): void
    {
        /** @todo Complete this unit test method. */
        $this->markTestIncomplete();
    }

    /**
     * @covers \App\Http\Controllers\UserController::show
     */
    public function testShow(): void
    {
        /** @todo Complete this unit test method. */
        $this->markTestIncomplete();
    }

    /**
     * @covers \App\Http\Controllers\UserController::update
     */
    public function testUpdate(): void
    {
        /** @todo Complete this unit test method. */
        $this->markTestIncomplete();
    }

    /**
     * @covers \App\Http\Controllers\UserController::destroy
     */
    public function testDestroy(): void
    {
        /** @todo Complete this unit test method. */
        $this->markTestIncomplete();
    }
}
