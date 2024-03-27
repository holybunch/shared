<?php

declare(strict_types=1);

namespace holybunch\shared\tests\google\youtube\apis;

use Fig\Http\Message\StatusCodeInterface;
use Google_Client;
use holybunch\shared\tests\BaseTest;
use Google\Service\YouTube\Resource\PlaylistItems;
use holybunch\shared\exceptions\SharedException;
use holybunch\shared\google\youtube\apis\PlaylistItemsAPI;
use PHPUnit\Framework\MockObject\MockObject;

final class PlaylistItemsAPITest extends BaseTest
{
    private PlaylistItems&MockObject $playlistItemsMock;
    private PlaylistItemsAPI $playlistItemsAPI;

    public function setUp(): void
    {
        $this->playlistItemsAPI = new PlaylistItemsAPI(new Google_Client());

        $this->playlistItemsMock = $this->getMockBuilder(PlaylistItems::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testPlaylistVideoIdsHappy(): void
    {
        // Configure the mock to expect two calls to listPlaylistItems
        // with specific arguments and return values
        $this->playlistItemsMock->expects($this->exactly(2))
            ->method("listPlaylistItems")
            ->willReturnCallback(function (string $part, array $params) {
                // Check arguments for the first call (no pageToken)
                if ($part === 'snippet' && $params === [
                    'playlistId' => "example_id_1",
                    'maxResults' => '50',
                ]) {
                    return ['items' => $this->demoItems(1), 'nextPageToken' => 'any'];
                } elseif ($part === 'snippet' && $params === [
                    // Check arguments for the second call (with pageToken)
                    'playlistId' => "example_id_1",
                    'maxResults' => '50',
                    'pageToken' => 'any',
                ]) {
                    return ['items' => $this->demoItems(1), 'nextPageToken' => null];
                } else {
                    $this->fail("check failed"); // Optional: Handle unexpected arguments
                }
            });

        $this->playlistItemsAPI->playlistItems = $this->playlistItemsMock;

        $result = $this->playlistItemsAPI->playlistVideoIds("example_id_1", 15);
        $this->assertNotEmpty($result);
        $this->assertCount(15, $result);
        $this->assertEquals("video-2-1", $result[1]);
    }

    public function testPlaylistVideoIdsFailed(): void
    {
        $this->playlistItemsMock->expects($this->once())
            ->method("listPlaylistItems")
            ->with('snippet', [
                'playlistId' => "example_id_1",
                'maxResults' => '50'
            ])
            ->willThrowException(new \Google\Service\Exception("error ocurred"));

        $this->playlistItemsAPI->playlistItems = $this->playlistItemsMock;

        $this->expectException(SharedException::class);
        $this->expectExceptionCode(StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
        $this->expectExceptionMessage("error ocurred");
        $this->playlistItemsAPI->playlistVideoIds("example_id_1", 5);
    }

    /** @phpstan-ignore-next-line */
    private function demoItems(int $suffix): array
    {
        $items = [];
        for ($i = 1; $i < 10; $i++) {
            $items[] = ['snippet' => ['resourceId' => ['videoId' => "video-$i-$suffix"]]];
        }
        return $items;
    }
}
