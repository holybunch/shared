<?php

declare(strict_types=1);

namespace holybunch\shared\tests\google\youtube\apis;

use Fig\Http\Message\StatusCodeInterface;
use Google_Client;
use holybunch\shared\tests\BaseTest;
use Google\Service\YouTube\Resource\Videos;
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
        $this->videosMock->expects($this->once())
            ->method("listVideos")
            ->with('snippet,liveStreamingDetails,statistics,contentDetails', [
                'id' => "video1,video2,video3",
                'maxResults' => '50'
            ])
            ->willReturn($this->demoVideos());

        $this->videosAPI->videos = $this->videosMock;

        $result = $this->videosAPI->videos([
            "video1", "video2", "video3"
        ]);
        $this->assertIsArray($result);
        $this->assertCount(3, $result);
        $this->assertEquals("video1", $result[0]->getId());
        $this->assertEquals("Sample Video Title 1", $result[0]->getTitle());
        $this->assertEquals("https://example.com/thumbnail.jpg", $result[0]->getThumbnail());
        $this->assertEquals("2024/03/17 08:03:00", $result[0]->getPublishedAt());
        $this->assertEquals("100", $result[0]->getLikeCount());
        $this->assertEquals("1000", $result[0]->getViewCount());
        $this->assertEquals("01:30:15", $result[0]->getDuration());
    }

    private function demoVideos()
    {
        $items = [];
        for ($i = 1; $i < 4; $i++) {
            $items[] = [
                'id' => 'video' . $i,
                'snippet' => [
                    'title' => 'Sample Video Title ' . $i,
                    'thumbnails' => [
                        'medium' => [
                            'url' => 'https://example.com/thumbnail.jpg'
                        ]
                    ],
                    'publishedAt' => '2024-03-17T08:00:00Z' 
                ],
                'contentDetails' => [
                    'duration' => 'PT1H30M15S' 
                ],
                'statistics' => [
                    'likeCount' => 100,
                    'viewCount' => 1000
                ]
            ];
        }

        return [
            "items" => $items
        ];
    }
}