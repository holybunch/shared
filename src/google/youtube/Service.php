<?php

namespace holybunch\shared\google\youtube;

use Exception;
use Google\Service\YouTube;
use Google_Client;
use holybunch\shared\exceptions\SharedException;

/**
 * Service class for interacting with the YouTube Data API.
 * Extends the base YouTube class from the Google API Client Library for PHP.
 */
class Service extends YouTube
{
    private const PLAYLIST_PART = 'snippet,id,contentDetails';

    /**
     * Constructs a Service object.
     *
     * @param Google_Client $client An authorized Google API client instance.
     */
    public function __construct(Google_Client $client)
    {
        parent::__construct($client);
    }

    /**
     * Retrieves playlists for a given channel ID.
     *
     * @param string $channelId The ID of the channel to retrieve playlists from.
     * @param string $part Comma-separated list of parts to include in the response.
     *               Defaults to snippet,id,contentDetails.
     * @return PlaylistObject[] An array of PlaylistObject instances representing the retrieved playlists.
     * @throws SharedException If an error occurs while retrieving playlists.
     */
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
