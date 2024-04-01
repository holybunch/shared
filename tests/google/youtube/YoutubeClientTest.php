<?php
declare(strict_types=1);
namespace holybunch\shared\tests\google\youtube;

use Fig\Http\Message\StatusCodeInterface;
use Google_Client;
use holybunch\shared\exceptions\SharedException;
use holybunch\shared\google\ClientBase;
use holybunch\shared\google\youtube\Client as YoutubeClient;
use holybunch\shared\tests\BaseTest;

final class YoutubeClientTest extends BaseTest
{
    public function testCreateNoSessionHappy(): void
    {
        unset($_SESSION);
        $client = new YoutubeClient();
        $client->setScopes([]);
        $this->assertFalse(isset($_SESSION));
        $client = $client->create(parent::TMP_Y_CREDENTIALS, "65f8299c85c63");
        $this->assertNotNull($client);
        $this->assertInstanceOf(Google_Client::class, $client);
        $this->assertArrayHasKey(ClientBase::ACCESS_TOKEN . "YOUTUBE", $_SESSION);
        $this->assertArrayHasKey(ClientBase::ACCESS_TOKEN_EXP . "YOUTUBE", $_SESSION);
    }

    public function testCreateSessionHappy(): void
    {
        $client = new YoutubeClient();
        $client->setScopes([]);
        $_SESSION[ClientBase::ACCESS_TOKEN . "YOUTUBE"] = "access_token_123";
        $_SESSION[ClientBase::ACCESS_TOKEN_EXP . "YOUTUBE"] =  time() + 3600;
        $client = $client->create(parent::TMP_Y_CREDENTIALS, "65f8299c85c63");
        $this->assertNotNull($client);
        $this->assertInstanceOf(Google_Client::class, $client);
    }

    public function testCreateFailed(): void
    {
        $client = new YoutubeClient();
        $this->expectException(SharedException::class);
        $this->expectExceptionMessage("file \"not-existing.json\" does not exist");
        $this->expectExceptionCode(StatusCodeInterface::STATUS_BAD_REQUEST);
        $client->create("not-existing.json", "65f8299c85c63");
    }
}