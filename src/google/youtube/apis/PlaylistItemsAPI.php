<?php

namespace holybunch\shared\google\youtube\apis;

use Exception;
use Google\Service\YouTube;
use Google_Client;
use holybunch\shared\exceptions\SharedException;

/**
 * Represents a class for interacting with YouTube playlistItems.
 * Extends the base YouTube class to utilize the YouTube Data API.
 *
 * @author holybunch
 */
class PlaylistItemsAPI extends YouTube
{
    /** @var string[] */
    private array $videoItemIDs;

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
     * Retrieves video IDs from a given playlist recursively up to a maximum count if specified.
     *
     * @param string      $playlistId   The ID of the playlist to retrieve video IDs from.
     * @param int|null    $maxCount     Maximum number of video IDs to retrieve (optional).
     * @param string|null $pageToken Token for pagination (optional).
     *
     * @return string[] An array containing the retrieved video IDs.
     * @throws SharedException If an error occurs during the retrieval process.
     */
    public function retrievePlaylistVideoIds(string $playlistId, int $maxCount = null, string $pageToken = null): array
    {
        try {
            $this->videoItemIDs = [];
            $this->fetchVideoItemIdsRecursive($playlistId, $maxCount, $pageToken);
            return $this->videoItemIDs;
        } catch (Exception $e) {
            throw new SharedException($e);
        }
    }

    /**
     * Fetches video item IDs recursively from the YouTube API.
     *
     * @param string      $playlistId The ID of the playlist to fetch video item IDs from.
     * @param int|null    $maxCount   Maximum number of video IDs to retrieve (optional).
     * @param string|null $pageToken  Token for pagination (optional).
     *
     * @throws SharedException If an error occurs during the retrieval process.
     */
    private function fetchVideoItemIdsRecursive(string $playlistId, ?int $maxCount, ?string $pageToken): void
    {
        try {
            $result = $this->playlistItems->listPlaylistItems(
                'snippet',
                $this->buildPlaylistApiParams($playlistId, $pageToken)
            );

            foreach ($result['items'] as $item) {
                if ($this->isMaxValueOk($maxCount)) {
                    $this->videoItemIDs[] = $item['snippet']['resourceId']['videoId'];
                } else {
                    return;
                }
            }
            if ($this->isMaxValueOk($maxCount) && isset($result['nextPageToken'])) {
                $this->fetchVideoItemIdsRecursive($playlistId, $maxCount, $result['nextPageToken']);
            }
        } catch (Exception $e) {
            throw new SharedException($e);
        }
    }


    /**
     * Builds parameters for the YouTube API request.
     *
     * @param string      $playlistId The ID of the playlist.
     * @param string|null $pageToken  Token for pagination (optional).
     *
     * @return array<string, string> The prepared optional parameters.
     */
    private function buildPlaylistApiParams(string $playlistId, ?string $pageToken): array
    {
        $optParams = [
            'playlistId' => $playlistId,
            'maxResults' => '50'
        ];
        if ($pageToken !== null) {
            $optParams['pageToken'] = $pageToken;
        }
        return $optParams;
    }


    /**
     * Checks if the maximum count is satisfied.
     *
     * @param int|null $maxCount Maximum number of video IDs to retrieve (optional).
     *
     * @return bool True if the maximum count is not reached or not specified, false otherwise.
     */
    private function isMaxValueOk(?int $maxCount): bool
    {
        return $maxCount === null || $maxCount >= (count($this->videoItemIDs) + 1);
    }
}
