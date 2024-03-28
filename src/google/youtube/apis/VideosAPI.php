<?php

namespace holybunch\shared\google\youtube\apis;

use Exception;
use Google\Service\YouTube;
use Google\Service\YouTube\VideoListResponse;
use Google_Client;
use holybunch\shared\exceptions\SharedException;
use holybunch\shared\google\youtube\VideoObject;

/**
 * Represents a class for interacting with YouTube videos.
 * Extends the base YouTube class to utilize the YouTube Data API.
 *
 * @author holybunch
 */
class VideosAPI extends YouTube
{
    private const int MAX_RESULT = 45;
    private array $videoObjects = [];

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

    public function videos(array $videoIds, int $max = 50): array
    {
        try {
            $this->videoObjects = [];
            foreach (array_chunk($videoIds, self::MAX_RESULT) as $singleArray) {
                $this->processVideos($singleArray, $max);
            }
            return $this->videoObjects;
        } catch (Exception $e) {
            throw new SharedException($e);
        }
    }
    
    private function processVideos(array $videoIds, int $max): void
    {
        $response = $this->videos->listVideos(
            'snippet,liveStreamingDetails,statistics,contentDetails',
            [
                'id' => implode(",", $videoIds),
                'maxResults' => self::MAX_RESULT
            ]
        );

        foreach ($response["items"] as $item) {
            if (count($this->videoObjects) >= $max) {
                return;
            }
            $this->videoObjects[] = new VideoObject($item);
        }
    }
}
