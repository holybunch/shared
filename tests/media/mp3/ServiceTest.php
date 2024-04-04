<?php

declare(strict_types=1);

namespace holybunch\shared\tests\media\mp3;

use holybunch\shared\tests\BaseTest;

final class ServiceTest extends BaseTest
{
   
    protected function tearDown(): void
    {
        $this->assertTrue(is_dir(parent::MEDIA_MP3));
    }

    public function testCollectionSongsHappy(): void
    {
       
    }
}
