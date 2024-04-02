<?php declare(strict_types=1);
namespace holybunch\shared\tests\security;

use holybunch\shared\exceptions\SharedException;
use holybunch\shared\security\JWTHandler;
use holybunch\shared\security\JWTModel;
use holybunch\shared\tests\BaseTest;

final class JWTHandlerTest extends BaseTest
{
    private const ID = 1;
    private const EMAIL = "max@example.com";
    private const PICTURE = "picture-test-code";
    private const ENC = 'unittest-entcryprion';
    private const URL = "https://unittest.com";

    public function testJWTHappy(): void
    {
        $jwtObject = JWTHandler::read($this->writeJWT(), self::ENC);
        $this->assertIsObject($jwtObject);
        $this->assertNotNull($jwtObject, "JWT object is null after reading.");
        $this->assertEquals(self::ID, $jwtObject->user_id, "User ID in JWT object does not match expected value."); //@phpstan-ignore-line
        $this->assertEquals(self::EMAIL, $jwtObject->sub, "Email in JWT object does not match expected value."); //@phpstan-ignore-line
        $this->assertEquals(self::URL, $jwtObject->iss, "Issuer in JWT object does not match expected value."); //@phpstan-ignore-line
        $this->assertEquals(self::PICTURE, $jwtObject->picture, "Picture code in JWT object does not match expected value."); //@phpstan-ignore-line
    }
 
    public function testJWTExpiredToken(): void
    {        
        $this->expectException(SharedException::class);
        $this->expectExceptionMessage("Expired token");
        JWTHandler::read($this->writeJWT(0), self::ENC);
    }

    private function writeJWT(int $exp = 10): string {
        $model = new JWTModel();
        $model->setIss(self::URL);
        $model->setUserId(self::ID);
        $model->setSub(self::EMAIL);
        $model->setPicture(self::PICTURE);

        $jwt = JWTHandler::write($model, self::ENC, $exp);
        $this->assertNotEmpty($jwt, "JWT object is empty after writing.");
        return $jwt;
    }
}