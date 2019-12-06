<?php



use App\Media;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

require('vendor/autoload.php');

/**
 * Class MediaTest.
 *
 * @covers \App\Media
 */
class MediaTest extends TestCase
{
    /**
     * @var Media $media An instance of "Media" to test.
     */
    private $media;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->media = Media::all()->random();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        $this->beforeApplicationDestroyed(function () {
            DB::disconnect();
        });
        $this->media = null;
        parent::tearDown();
    }

    /**
     * @covers \App\Media::files
     * @covers \App\Media::actors
     * @covers \App\Media::ratings
     */
    public function testMedia(): void
    {
        $this->setUp();
        $response = $this->media->with('files', 'actors', 'ratings')->get();
        $this->assertNotEmpty($response);
        $this->assertInstanceOf('\Illuminate\Database\Eloquent\Collection', $response);
        foreach($response as $entry)
        {
            $this->assertTrue($response->contains('files', $entry->files));
            $this->assertTrue($response->contains('actors', $entry->actors));
            $this->assertTrue($response->contains('ratings', $entry->ratings));
        }        
        $this->tearDown();
    }


}
