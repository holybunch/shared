<?php

declare(strict_types=1);

namespace holybunch\shared\tests\logs;

use holybunch\shared\tests\BaseTest;
use holybunch\shared\logs\Logger;
use Monolog\Level;
use Monolog\Logger as MonologLogger;

final class LoggerTest extends BaseTest
{
    private string $file = "tests/.tmp/.logs/%s/log-%s-%s.log";
    private MonologLogger $logger;

    protected function tearDown(): void
    {
        unlink(sprintf($this->file, date("Y"), date("m"), date("d")));
    }

    public function testDebugHappy(): void
    {
        $this->logger = Logger::build("tests/.tmp/.logs/", Level::Debug);
        $this->logger->debug("message", ["type" => "debug"]);
        $this->logger->info("message", ["type" => "info"]);
        $this->logger->warning("message", ["type" => "warning"]);
        $this->logger->error("message", ["type" => "error"]);

        $logContents = file_get_contents(
            sprintf($this->file, date("Y"), date("m"), date("d"))
        );
        $this->assertIsString($logContents);
        $this->assertStringContainsString('{"type":"debug"}', $logContents);
        $this->assertStringContainsString('{"type":"info"}', $logContents);
        $this->assertStringContainsString('{"type":"warning"}', $logContents);
        $this->assertStringContainsString('{"type":"error"}', $logContents);
    }
}
