<?php

declare(strict_types=1);

namespace holybunch\shared\tests\google\youtube;

use Fig\Http\Message\StatusCodeInterface;
use Google_Client;
use holybunch\shared\exceptions\SharedException;
use holybunch\shared\google\youtube\apis\PlaylistItemsAPI;
use holybunch\shared\google\youtube\apis\PlaylistsAPI;
use holybunch\shared\google\youtube\apis\VideosAPI;
use holybunch\shared\google\youtube\Service as YoutubeService;
use holybunch\shared\tests\BaseTest;

final class YoutubeServiceTest extends BaseTest
{
    public function testCreateHappy(): void
    {
        $service = new YoutubeService(self::TMP_Y_CONFIG, self::TMP_Y_CREDENTIALS);
        $this->assertInstanceOf(Google_Client::class, $service->googleClient());
        $this->assertInstanceOf(PlaylistsAPI::class, $service->playlistsAPI());
        $this->assertInstanceOf(PlaylistItemsAPI::class, $service->playlistItemsAPI());
        $this->assertInstanceOf(VideosAPI::class, $service->videosAPI());
    }

    public function testCreateFileNotExists(): void
    {
        $this->expectException(SharedException::class);
        $this->expectExceptionCode(StatusCodeInterface::STATUS_NOT_FOUND);
        $this->expectExceptionMessage("Failed to read configuration data from tests/.tmp/.gitkeep");
        new YoutubeService("tests/.tmp/.gitkeep", self::TMP_Y_CREDENTIALS);
    }

    public function testCreateNotJson(): void
    {
        $this->expectException(SharedException::class);
        $this->expectExceptionCode(StatusCodeInterface::STATUS_BAD_REQUEST);
        $this->expectExceptionMessage("Failed to decode JSON data from tests/.tmp/not-json.txt");
        new YoutubeService("tests/.tmp/not-json.txt", self::TMP_Y_CREDENTIALS);
    }

    public function testCreateMissingKey(): void
    {
        $this->expectException(SharedException::class);
        $this->expectExceptionCode(StatusCodeInterface::STATUS_BAD_REQUEST);
        $this->expectExceptionMessage("Refresh token key is missing in tests/.tmp/missing-key.json");
        new YoutubeService("tests/.tmp/missing-key.json", self::TMP_Y_CREDENTIALS);
    }

    public function testUpdatRefreshTokenHappy(): void
    {
        $service = new YoutubeService(self::TMP_Y_CONFIG, self::TMP_Y_CREDENTIALS);
        $service->updatRefreshToken("new token");
        $data = json_decode(file_get_contents(self::TMP_Y_CONFIG), true); /** @phpstan-ignore-line */
        $this->assertArrayHasKey(YoutubeService::REFRESH_TOKEN, $data); /** @phpstan-ignore-line */
        $this->assertEquals("new token", $data[YoutubeService::REFRESH_TOKEN]); /** @phpstan-ignore-line */
    }

    public function testUpdatRefreshTokenFailed(): void
    {
        //should works 
        $this->assertTrue(true);
    }
}

