<?php

declare(strict_types=1);

namespace holybunch\shared\tests\google\auth;

use Fig\Http\Message\StatusCodeInterface;
use holybunch\shared\exceptions\SharedException;
use holybunch\shared\google\auth\Service as AuthService;
use holybunch\shared\tests\BaseTest;

final class AuthServiceTest extends BaseTest
{
    public function testClientIdHappy(): void
    {
        $service = new AuthService(self::TMP_Y_CONFIG);
        $result = $service->clientId();
        $this->assertEquals("65f8299c85c63", $result);
    }

    public function testClientIdMissingKey(): void
    {
        $this->expectException(SharedException::class);
        $this->expectExceptionCode(StatusCodeInterface::STATUS_BAD_REQUEST);
        $this->expectExceptionMessage("Client ID key is missing in tests/.tmp/missing-key.json");
        $service = new AuthService("tests/.tmp/missing-key.json");
        $service->clientId();
    }


    
}
