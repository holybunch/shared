<?php

declare(strict_types=1);

namespace holybunch\shared\tests\media\mp3;

use Fig\Http\Message\StatusCodeInterface;
use holybunch\shared\exceptions\SharedException;
use holybunch\shared\media\mp3\Service as MusicService;
use holybunch\shared\tests\BaseTest;

final class ServiceTest extends BaseTest
{

    private MusicService $service;
    protected function setUp(): void
    {
        $this->assertTrue(is_dir(parent::MEDIA_MP3));
        $this->service = new MusicService(parent::MEDIA_MP3);
    }

    public function testSongsHappy(): void
    {
        $result = $this->service->songs("happy");
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals("music-file-ok.mp3", $result[0]->getFileName());
        $this->assertEquals("Die Sonne scheint am Himmel klar © HOLYBUNCH.COM", $result[0]->getTitle());
        $this->assertEquals("LD", $result[0]->getArtist());
        $this->assertEquals("Gottes Kinder", $result[0]->getAlbum());
        $this->assertEquals("2017", $result[0]->getYear());
        $this->assertEquals("N/A", $result[0]->getComment());
        $this->assertEquals("6,1", $result[0]->getFilesize());

        $result = $this->service->exclusions();
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals(
            "error for the file 'tests/.tmp/mp3/happy/04-01.mp3' occurred: " .
                "File tests/.tmp/mp3/happy/04-01.mp3 is not mpeg/audio!",
            $result[0]
        );
    }

    public function testSongsNotFound(): void
    {
        $this->expectException(SharedException::class);
        $this->expectExceptionMessage("No such file or directory");
        $this->expectExceptionCode(StatusCodeInterface::STATUS_NOT_FOUND);
        $this->service->songs("unhappy");
    }

    public function testCollectionsHappy(): void
    {
        $result = $this->service->collections();
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals("happy", $result[0]);
    }
}
