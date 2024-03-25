<?php
declare(strict_types=1);
namespace holybunch\shared\tests\google\youtube;

use Fig\Http\Message\StatusCodeInterface;
use Google_Client;
use holybunch\shared\google\youtube\Service as YoutubeService;
use holybunch\shared\tests\BaseTest;
use Google\Service\YouTube\Resource\Playlists;
use holybunch\shared\exceptions\SharedException;
use holybunch\shared\google\youtube\PlaylistObject;
use PHPUnit\Framework\MockObject\MockObject;

final class ServiceTest extends BaseTest
{
    private Playlists&MockObject $playlistsMock;
    private YoutubeService $service;

    public function setUp(): void
    {
        $this->service = new YoutubeService(new Google_Client());

        $this->playlistsMock = $this->getMockBuilder(Playlists::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testYoutubeWithoutSessionHappy(): void
    {
        $this->playlistsMock->expects($this->once())
            ->method("listPlaylists")
            ->with('snippet,id,contentDetails', ['channelId' => "my-channel"])
            ->willReturn($this->demoPlaylists());

        $this->service->playlists = $this->playlistsMock;
        $result = $this->service->getPlaylists("my-channel");
        $this->assertIsArray($result);
        $this->assertCount(3, $result);
        $this->assertInstanceOf(PlaylistObject::class, $result[1]);
        $this->assertEquals("example_id_2", $result[1]->getId());
        $this->assertEquals("Example Title 2", $result[1]->getTitle());
        $this->assertEquals("Example Description 2", $result[1]->getDescription());
        $this->assertEquals("http://example.com/image2.jpg", $result[1]->getThumbnail());
        $this->assertEquals(10, $result[1]->getItemCount());
    }

    public function testPlaylistsFailed(): void
    {
        $this->playlistsMock->expects($this->once())
            ->method("listPlaylists")
            ->with('snippet,id,contentDetails', ['channelId' => "my-channel"])
            ->willThrowException(new \Google\Service\Exception("error ocurred"));

        $this->service->playlists = $this->playlistsMock;
        
        $this->expectException(SharedException::class);
        $this->expectExceptionCode(StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
        $this->expectExceptionMessage("error ocurred");
        $this->service->getPlaylists("my-channel");
    }

    private function demoPlaylists()
    {
        $items = [];
        for ($i = 1; $i < 4; $i++) {
            $items[] = [
                "id" => "example_id_$i",
                "snippet" => [
                    "title" => "Example Title $i",
                    "description" => "Example Description $i",
                    "thumbnails" => [
                        "medium" => [
                            "url" => "http://example.com/image$i.jpg"
                        ]
                    ]
                ],
                "contentDetails" => [
                    "itemCount" => 10
                ]
            ];
        }

        return [
            "items" => $items
        ];
    }
}