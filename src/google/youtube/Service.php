<?php

namespace holybunch\shared\google\youtube;

use Exception;
use Google\Service\YouTube;
use Google_Client;
use holybunch\shared\exceptions\SharedException;

class Service extends YouTube
{
    private const PLAYLIST_PART = 'snippet,id,contentDetails';

    public function __construct(Google_Client $client)
    {
        parent::__construct($client);
    }

    public function getPlaylists(string $channelId, string $part = self::PLAYLIST_PART): array
    {
        try {
            $result = $this->playlists->listPlaylists(
                $part,
                [ 'channelId' => $channelId ]
            );

            $playlists = [];
            foreach ($result['items'] as $item) {
                $playlists[] = new PlaylistObject($item);
            }

            return $playlists;
        } catch (Exception $e) {
            throw new SharedException($e);
        }
    }
}
