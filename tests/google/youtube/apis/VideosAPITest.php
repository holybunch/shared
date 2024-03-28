<?php

declare(strict_types=1);

namespace holybunch\shared\tests\google\youtube\apis;

use Fig\Http\Message\StatusCodeInterface;
use Google\Service\YouTube\Resource\Thumbnails;
use Google_Client;
use holybunch\shared\tests\BaseTest;
use Google\Service\YouTube\Resource\Videos;
use Google\Service\YouTube\Thumbnail;
use Google\Service\YouTube\ThumbnailDetails;
use Google\Service\YouTube\Video;
use Google\Service\YouTube\VideoContentDetails;
use Google\Service\YouTube\VideoSnippet;
use Google\Service\YouTube\VideoStatistics;
use holybunch\shared\exceptions\SharedException;
use holybunch\shared\google\youtube\apis\VideosAPI;
use PHPUnit\Framework\MockObject\MockObject;

final class VideosAPITest extends BaseTest
{
    private Videos&MockObject $videosMock;
    private VideosAPI $videosAPI;

    public function setUp(): void
    {
        $this->videosAPI = new VideosAPI(new Google_Client());

        $this->videosMock = $this->getMockBuilder(Videos::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testVideosHappy(): void
    {
        $counter = 0;
        $this->videosMock->expects($this->exactly(2))
            ->method("listVideos")
            ->with(
                $this->equalTo('snippet,liveStreamingDetails,statistics,contentDetails'),
                $this->callback(function($arg) use (&$counter){
                    ++$counter;
                   return 
                        (str_starts_with($arg["id"], "video1,") && str_ends_with($arg["id"], ",video30")) || 
                        (str_starts_with($arg["id"], "video31,") && str_ends_with($arg["id"], ",video49"));
                })
            )
            ->willReturnCallback(function($arr) use (&$counter){
                return $this->demoVideos($counter);
            });

        $this->videosAPI->videos = $this->videosMock;

        $result = $this->videosAPI->videos($this->demoIds());
        $this->assertIsArray($result);
        $this->assertCount(50, $result);
        $this->assertEquals("video1", $result[0]->getId());
        $this->assertEquals("Sample Video Title 1", $result[0]->getTitle());
        $this->assertEquals("https://example.com/thumbnail.jpg", $result[0]->getThumbnail());
        $this->assertEquals("2024/03/17 08:03:00", $result[0]->getPublishedAt());
        $this->assertEquals("100", $result[0]->getLikeCount());
        $this->assertEquals("1000", $result[0]->getViewCount());
        $this->assertEquals("01:30:15", $result[0]->getDuration());
        $this->assertNull($result[9]->getPublishedAt());
    }

    public function testVideosFailed(): void
    {
        $this->videosMock->expects($this->once())
            ->method("listVideos")
            ->with(
                $this->equalTo('snippet,liveStreamingDetails,statistics,contentDetails'),
                $this->callback(function($arg){
                   return (str_starts_with($arg["id"], "video1,") && str_ends_with($arg["id"], ",video30"));
                })
            )
            ->willThrowException(new \Google\Service\Exception("error ocurred"));

        $this->videosAPI->videos = $this->videosMock;

        $this->expectException(SharedException::class);
        $this->expectExceptionCode(StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
        $this->expectExceptionMessage("error ocurred");
        $this->videosAPI->videos($this->demoIds());
    }

    /** @return string[] */
    private function demoIds(): array 
    {
        $ids = [];
        for ($i=1; $i < 50; $i++) { 
            $ids[] = "video$i";
        }
        return $ids;
    }

    /** @phpstan-ignore-next-line */
    private function demoVideos(int $counter): array
    {
        $number = ($counter == 1) ? 30 : 20;
        $items = [];
        for ($i = 1; $i <= $number; $i++) {
            $video = new Video();
            $video->setId('video' . $i);
            $thumbnail = new Thumbnail();
            $thumbnail->setUrl('https://example.com/thumbnail.jpg');
            $thumbnailDetails = new ThumbnailDetails();
            $thumbnailDetails->setMedium($thumbnail);
            $videoSnippet = new VideoSnippet();
            $videoSnippet->setTitle('Sample Video Title ' . $i);
            $videoSnippet->setThumbnails($thumbnailDetails);
            $videoSnippet->setPublishedAt('2024-03-17T08:00:00Z');            
            if ($i == 10) {
                $videoSnippet->setPublishedAt(null);
            }
            $video->setSnippet($videoSnippet);
            $contentDetails = new VideoContentDetails();
            $contentDetails->setDuration('PT1H30M15S');
            $video->setContentDetails($contentDetails);
            $statistics = new VideoStatistics();
            $statistics->setLikeCount("100");
            $statistics->setViewCount("1000");
            $video->setStatistics($statistics);

            $items[] = $video;
        }

        return [
            "items" => $items
        ];
    }
}
