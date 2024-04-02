<?php

declare(strict_types=1);

namespace holybunch\shared\tests\security;

use Fig\Http\Message\StatusCodeInterface;
use holybunch\shared\exceptions\BadRequestException;
use holybunch\shared\security\Encryption;
use holybunch\shared\tests\BaseTest;

final class EncriptionTest extends BaseTest
{
    private const VALUE = "test";
    private const KEY = "key";

    public function testEncryptHappy(): void
    {
        $en = Encryption::encrypt(self::VALUE, self::KEY);
        $this->assertNotEmpty($en, "The encrypted value is empty.");
    }
    
    public function testDecryptHappy(): void {
        $de = Encryption::decrypt("2p+doGFTzYIn/+0IWLZkTXJKZmtGV2gzYURsMDl2NU9xSlJ0d0E9PQ==", self::KEY);
        $this->assertNotEmpty($de, "The decrypted value is empty.");
        $this->assertEquals($de, self::VALUE, "The decrypted value does not match the expected value.");
    }
    
    public function testDecryptInvalidBase64String(): void {
        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage("Invalid base64 encoded data");
        $this->expectExceptionCode(StatusCodeInterface::STATUS_BAD_REQUEST);
        Encryption::decrypt("", self::KEY);
    }
    
    public function testDecryptFailed(): void {
        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage("Unable to decrypt the data");
        $this->expectExceptionCode(StatusCodeInterface::STATUS_BAD_REQUEST);
        Encryption::decrypt("2p+doGFTzYIn/+0IWLZkTXJKZmtGV2gzYURsMDl2NU9xSlJ0d0E9PQ==", "other");
    }
}
