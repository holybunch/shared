<?php

namespace holybunch\shared\google\youtube;

use holybunch\shared\google\ClientBase;
use Google\Service\YouTube;

/**
 * Google YouTube API client class.
 *
 * @final
 */
final class Client extends ClientBase
{
    /**
     * Client constructor.
     */
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
