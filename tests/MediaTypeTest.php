<?php



use App\MediaType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

require('vendor/autoload.php');

/**
 * Class MediaTypeTest.
 *
 * @covers \App\MediaType
 */
class MediaTypeTest extends TestCase
{
    /**
     * @var MediaType $mediatype An instance of "MediaType" to test.
     */
    private $mediatype;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->mediatype = MediaType::all()->random();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        $this->beforeApplicationDestroyed(function () {
            DB::disconnect();
        });
        $this->mediatype = null;
        parent::tearDown();
    }

    /**
     * @covers \App\MediaType::categories
     * @covers \App\MediaType::media
     */
    public function testMediaType(): void
    {
        $this->setUp();
        $response = $this->mediatype->with('categories', 'media')->get();
        $this->assertNotEmpty($response);
        $this->assertInstanceOf('\Illuminate\Database\Eloquent\Collection', $response);
        foreach($response as $entry)
        {
            $this->assertTrue($response->contains('categories', $entry->categories));
            $this->assertTrue($response->contains('media', $entry->media));
        }        
        $this->tearDown();
    }


}
