<?php
namespace holybunch\shared\tests;

use PHPUnit\Framework\TestCase;

abstract class BaseTest extends TestCase
{
    public const TMP_Y_CREDENTIALS = "tests/.tmp/credentials.json";

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::assertIsInt(file_put_contents(self::TMP_Y_CREDENTIALS, '{"web":{"client_id":"unittest","project_id":"holybunch-youtube-unittest","auth_uri":"https://auth_url.com","token_uri":"https://token_url.com","auth_provider_x509_cert_url":"https://www.certs.com","client_secret":"hided","redirect_uris":["http://localhost/settings/token/"]}}'));
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        unlink(self::TMP_Y_CREDENTIALS);
    }
}