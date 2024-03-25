<?php
declare(strict_types=1);

use Fig\Http\Message\StatusCodeInterface;
use holybunch\shared\exceptions\SharedException;
use holybunch\shared\exceptions\GoogleAPIException;
use holybunch\shared\google\youtube\Client as YoutubeClient;
use holybunch\shared\tests\BaseTest;

final class ClientTest extends BaseTest
{
    public function testYoutubeHappy(): void
    {
        $client = new YoutubeClient();
        $client->setScopes([]);
        $client = $client->create(parent::TMP_Y_CREDENTIALS, "65f8299c85c63");
        $this->assertNotNull($client);
        $this->assertInstanceOf(Google_Client::class, $client);
    }

    public function testYoutubeFailed(): void
    {
        $client = new YoutubeClient();
        $this->expectException(SharedException::class);
        $this->expectExceptionMessage("file \"not-existing.json\" does not exist");
        $this->expectExceptionCode(StatusCodeInterface::STATUS_BAD_REQUEST);
        $client->create("not-existing.json", "65f8299c85c63");
    }
}