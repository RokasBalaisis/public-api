<?php

namespace Test\App\Http\Controllers;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use App\Http\Controllers\RatingController;
use Illuminate\Http\Request;

/**
 * Class RatingControllerTest.
 *
 * @covers \App\Http\Controllers\RatingController
 */
class RatingControllerTest extends TestCase
{
    /**
     * @var RatingController $ratingController An instance of "RatingController" to test.
     */
    private $ratingController;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        /** @todo Maybe add some arguments to this constructor */
        $this->ratingController = new RatingController();
    }

    /**
     * @covers \App\Http\Controllers\RatingController::index
     */
    public function testIndex(): void
    {
        /** @todo Complete this unit test method. */
        $this->markTestIncomplete();
    }

    /**
     * @covers \App\Http\Controllers\RatingController::store
     */
    public function testStore(): void
    {
        /** @todo Complete this unit test method. */
        $this->markTestIncomplete();
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
}
