<?php

declare(strict_types=1);

namespace holybunch\shared\tests\google\calendar;

use Fig\Http\Message\StatusCodeInterface;
use Google_Client;
use holybunch\shared\exceptions\SharedException;
use holybunch\shared\google\ClientBase;
use holybunch\shared\google\calendar\Client as CalendarClient;
use holybunch\shared\tests\BaseTest;

final class CalendarClientTest extends BaseTest
{
    public function testCreateNoSessionHappy(): void
    {
        unset($_SESSION);
        $client = new CalendarClient();
        $client->setScopes([]);
        $this->assertFalse(isset($_SESSION));
        $client = $client->create(parent::TMP_Y_CREDENTIALS, "65f8299c85c63");
        $this->assertNotNull($client);
        $this->assertInstanceOf(Google_Client::class, $client);
        $this->assertArrayHasKey(ClientBase::ACCESS_TOKEN, $_SESSION);
        $this->assertArrayHasKey(ClientBase::ACCESS_TOKEN_EXP, $_SESSION);
    }

    public function testCreateSessionHappy(): void
    {
        $client = new CalendarClient();
        $client->setScopes([]);
        $_SESSION[ClientBase::ACCESS_TOKEN] = "access_token_123";
        $_SESSION[ClientBase::ACCESS_TOKEN_EXP] =  time() + 3600;
        $client = $client->create(parent::TMP_Y_CREDENTIALS, "65f8299c85c63");
        $this->assertNotNull($client);
        $this->assertInstanceOf(Google_Client::class, $client);
    }

    public function testCreateFailed(): void
    {
        $client = new CalendarClient();
        $this->expectException(SharedException::class);
        $this->expectExceptionMessage("file \"not-existing.json\" does not exist");
        $this->expectExceptionCode(StatusCodeInterface::STATUS_BAD_REQUEST);
        $client->create("not-existing.json", "65f8299c85c63");
    }
}
