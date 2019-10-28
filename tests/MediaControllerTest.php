<?php

namespace Test\App\Http\Controllers;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use App\Http\Controllers\MediaController;
use Illuminate\Http\Request;

/**
 * Class MediaControllerTest.
 *
 * @covers \App\Http\Controllers\MediaController
 */
class MediaControllerTest extends TestCase
{
    /**
     * @var MediaController $mediaController An instance of "MediaController" to test.
     */
    private $mediaController;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        /** @todo Maybe add some arguments to this constructor */
        $this->mediaController = new MediaController();
    }

    /**
     * @covers \App\Http\Controllers\MediaController::index
     */
    public function testIndex(): void
    {
        /** @todo Complete this unit test method. */
        $this->markTestIncomplete();
    }

    /**
     * @covers \App\Http\Controllers\MediaController::store
     */
    public function testStore(): void
    {
        /** @todo Complete this unit test method. */
        $this->markTestIncomplete();
    }

    /**
     * @covers \App\Http\Controllers\MediaController::show
     */
    public function testShow(): void
    {
        /** @todo Complete this unit test method. */
        $this->markTestIncomplete();
    }

    /**
     * @covers \App\Http\Controllers\MediaController::update
     */
    public function testUpdate(): void
    {
        /** @todo Complete this unit test method. */
        $this->markTestIncomplete();
    }

    /**
     * @covers \App\Http\Controllers\MediaController::destroy
     */
    public function testDestroy(): void
    {
        /** @todo Complete this unit test method. */
        $this->markTestIncomplete();
    }
}
