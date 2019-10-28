<?php

namespace Test\App\Http\Controllers;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use App\Http\Controllers\Controller;

/**
 * Class ControllerTest.
 *
 * @covers \App\Http\Controllers\Controller
 */
class ControllerTest extends TestCase
{
    /**
     * @var Controller $controller An instance of "Controller" to test.
     */
    private $controller;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        /** @todo Maybe add some arguments to this constructor */
        $this->controller = new Controller();
    }

    /**
     * @covers \App\Http\Controllers\Controller::respondWithToken
     */
    public function testRespondWithToken(): void
    {
        /** @todo Complete this unit test method. */
        $this->markTestIncomplete();
    }
}
