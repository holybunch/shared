<?php

declare(strict_types=1);

namespace holybunch\shared\tests\google\youtube\apis;

use Fig\Http\Message\StatusCodeInterface;
use Google_Client;
use holybunch\shared\tests\BaseTest;
use Google\Service\YouTube\Resource\Playlists;
use holybunch\shared\exceptions\NotFoundException;
use holybunch\shared\exceptions\SharedException;
use holybunch\shared\google\youtube\apis\PlaylistsAPI;
use holybunch\shared\google\youtube\objects\PlaylistObject;
use PHPUnit\Framework\MockObject\MockObject;

final class PlaylistsAPITest extends BaseTest
{
    private Playlists&MockObject $playlistsMock;
    private PlaylistsAPI $playlistsAPI;

    public function setUp(): void
    {
        $this->playlistsAPI = new PlaylistsAPI(new Google_Client());

        $this->playlistsMock = $this->getMockBuilder(Playlists::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testAllHappy(): void
    {
        $this->playlistsMock->expects($this->once())
            ->method("listPlaylists")
            ->with('snippet,id,contentDetails', ['channelId' => "my-channel"])
            ->willReturn($this->demoPlaylists());

        $this->playlistsAPI->playlists = $this->playlistsMock;
        $result = $this->playlistsAPI->all("my-channel");
        $this->assertIsArray($result);
        $this->assertCount(3, $result);
        $this->assertInstanceOf(PlaylistObject::class, $result[1]);
        $this->assertEquals("example_id_2", $result[1]->getId());
        $this->assertEquals("Example Title 2", $result[1]->getTitle());
        $this->assertEquals("Example Description 2", $result[1]->getDescription());
        $this->assertEquals("http://example.com/image2.jpg", $result[1]->getThumbnail());
        $this->assertEquals(10, $result[1]->getItemCount());
    }

    public function testAllFailed(): void
    {
        $this->playlistsMock->expects($this->once())
            ->method("listPlaylists")
            ->with('snippet,id,contentDetails', ['channelId' => "my-channel"])
            ->willThrowException(new \Google\Service\Exception("error ocurred"));

        $this->playlistsAPI->playlists = $this->playlistsMock;

        $this->expectException(SharedException::class);
        $this->expectExceptionCode(StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
        $this->expectExceptionMessage("error ocurred");
        $this->playlistsAPI->all("my-channel");
    }

    public function testOneHappy(): void
    {
        $this->playlistsMock->expects($this->once())
            ->method("listPlaylists")
            ->with('snippet,id,contentDetails', ['id' => "example_id_1"])
            ->willReturn($this->demoPlaylists());

        $this->playlistsAPI->playlists = $this->playlistsMock;

        $result = $this->playlistsAPI->one("example_id_1");
        $this->assertInstanceOf(PlaylistObject::class, $result);
        $this->assertEquals("example_id_1", $result->getId());
    }

    public function testOneNotFound(): void
    {
        $this->playlistsMock->expects($this->once())
            ->method("listPlaylists")
            ->with('snippet,id,contentDetails', ['id' => "example_id_1"])
            ->willReturn(["items" => []]);

        $this->playlistsAPI->playlists = $this->playlistsMock;

        $this->expectException(NotFoundException::class);
        $this->expectExceptionCode(StatusCodeInterface::STATUS_NOT_FOUND);
        $this->expectExceptionMessage("Playlist for id 'example_id_1' is not found");
        $this->playlistsAPI->one("example_id_1");
    }

    public function testOneFailed(): void
    {
        $this->playlistsMock->expects($this->once())
            ->method("listPlaylists")
            ->with('snippet,id,contentDetails', ['id' => "example_id_1"])
            ->willThrowException(new \Google\Service\Exception("error ocurred"));

        $this->playlistsAPI->playlists = $this->playlistsMock;

        $this->expectException(SharedException::class);
        $this->expectExceptionCode(StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
        $this->expectExceptionMessage("error ocurred");
        $this->playlistsAPI->one("example_id_1");
    }

    /** @phpstan-ignore-next-line */
    private function demoPlaylists(): array
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
