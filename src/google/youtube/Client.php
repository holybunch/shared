<?php

namespace holybunch\shared\google\youtube;

use holybunch\shared\google\ClientBase;
use Google\Service\YouTube;

/**
 * Google YouTube API client class.
 *
 * @author holybunch
 */
class Client extends ClientBase
{
    /**
     * Constructs a new Client object for interacting with the Google YouTube API.
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
        $this->service = "YOUTUBE";
    }
}
