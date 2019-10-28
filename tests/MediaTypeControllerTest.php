<?php

namespace Test\App\Http\Controllers;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use App\Http\Controllers\MediaTypeController;
use Illuminate\Http\Request;

/**
 * Class MediaTypeControllerTest.
 *
 * @covers \App\Http\Controllers\MediaTypeController
 */
class MediaTypeControllerTest extends TestCase
{
    /**
     * @var MediaTypeController $mediaTypeController An instance of "MediaTypeController" to test.
     */
    private $mediaTypeController;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        /** @todo Maybe add some arguments to this constructor */
        $this->mediaTypeController = new MediaTypeController();
    }

    /**
     * @covers \App\Http\Controllers\MediaTypeController::index
     */
    public function testIndex(): void
    {
        /** @todo Complete this unit test method. */
        $this->markTestIncomplete();
    }

    /**
     * @covers \App\Http\Controllers\MediaTypeController::indexMedia
     */
    public function testIndexMedia(): void
    {
        /** @todo Complete this unit test method. */
        $this->markTestIncomplete();
    }

    /**
     * @covers \App\Http\Controllers\MediaTypeController::store
     */
    public function testStore(): void
    {
        /** @todo Complete this unit test method. */
        $this->markTestIncomplete();
    }

    /**
     * @covers \App\Http\Controllers\MediaTypeController::show
     */
    public function testShow(): void
    {
        /** @todo Complete this unit test method. */
        $this->markTestIncomplete();
    }

    /**
     * @covers \App\Http\Controllers\MediaTypeController::showMedia
     */
    public function testShowMedia(): void
    {
        /** @todo Complete this unit test method. */
        $this->markTestIncomplete();
    }

    /**
     * @covers \App\Http\Controllers\MediaTypeController::update
     */
    public function testUpdate(): void
    {
        /** @todo Complete this unit test method. */
        $this->markTestIncomplete();
    }

    /**
     * @covers \App\Http\Controllers\MediaTypeController::destroy
     */
    public function testDestroy(): void
    {
        /** @todo Complete this unit test method. */
        $this->markTestIncomplete();
    }
}
