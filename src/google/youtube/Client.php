<?php

namespace holybunch\shared\google\youtube;

use google\ClientBase;
use Google\Service\YouTube;

class Client extends ClientBase
{
    public function __construct()
    {
        parent::__construct([
            YouTube::YOUTUBE,
            YouTube::YOUTUBE_FORCE_SSL,
            YouTube::YOUTUBE_UPLOAD,
            YouTube::YOUTUBE_READONLY,
            YouTube::YOUTUBEPARTNER,
            YouTube::YOUTUBEPARTNER_CHANNEL_AUDIT
        ]);
    }
}
