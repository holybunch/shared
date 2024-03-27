<?php

namespace holybunch\shared\google\youtube;

use Exception;
use Google\Service\YouTube;
use Google_Client;
use holybunch\shared\exceptions\NotFoundException;
use holybunch\shared\exceptions\SharedException;

/**
 * Service class for interacting with the YouTube Data API.
 * Extends the base YouTube class from the Google API Client Library for PHP.
 *
 * @author holybunch
 */
class Service extends YouTube
{
    private const PLAYLIST_PART = 'snippet,id,contentDetails';

    /**
     * Constructs a new Service object for interacting with the YouTube Data API using
     * the provided Google API client instance.
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
     * @return PlaylistObject[] An array of PlaylistObject instances representing
     *                          the retrieved playlists.
     * @throws SharedException If an error occurs while retrieving playlists.
     */
    public function getPlaylists(string $channelId): array
    {
        try {
            $result = $this->playlists->listPlaylists(self::PLAYLIST_PART, ['channelId' => $channelId]);
            $playlists = [];
            foreach ($result['items'] as $item) {
                $playlists[] = new PlaylistObject($item);
            }
            return $playlists;
        } catch (Exception $e) {
            throw new SharedException($e);
        }
    }

    /**
     * Retrieves a playlist object by its ID.
     *
     * @param string $id The ID of the playlist to retrieve.
     * @return PlaylistObject The playlist object.
     * @throws NotFoundException If the playlist with the given ID is not found.
     * @throws SharedException If any other exception occurs during the retrieval process.
     */
    public function getPlaylist(string $id): PlaylistObject
    {
        try {
            $result = $this->playlists->listPlaylists(self::PLAYLIST_PART, ['id' => $id]);
            if (empty($result['items'])) {
                throw new NotFoundException("Playlist for id '$id' is not found");
            }
            return new PlaylistObject($result['items'][0]);
        } catch (NotFoundException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new SharedException($e);
        }
    }
}
