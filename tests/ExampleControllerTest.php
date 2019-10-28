<?php

namespace Test\App\Http\Controllers;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use App\Http\Controllers\ExampleController;

/**
 * Class ExampleControllerTest.
 *
 * @covers \App\Http\Controllers\ExampleController
 */
class ExampleControllerTest extends TestCase
{
    /**
     * @var ExampleController $exampleController An instance of "ExampleController" to test.
     */
    private $exampleController;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        /** @todo Maybe add some arguments to this constructor */
        $this->exampleController = new ExampleController();
    }

    /**
     * @covers \App\Http\Controllers\ExampleController::__construct
     */
    public function testConstruct(): void
    {
        /** @todo Complete this unit test method. */
        $this->markTestIncomplete();
    }
}
