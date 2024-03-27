<?php

namespace holybunch\shared\google\youtube\apis;

use Exception;
use Google\Service\YouTube;
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


    public function videosByIds(array $videoIds, int $max = 50): array
    {
        try {
            $videos = [];
            $all = array_chunk($videoIds, 45);
            foreach ($all as $singleArray) {
                $videos = array_merge($videos, $this->fetchVideos($singleArray, $max));
            }
            return $videos;
        } catch (Exception $e) {
            throw new SharedException($e);
        }
    }

    private function fetchVideos(array $videoIds, int $max): array
    {
        $response = $this->fetchVideosRecursive(implode(",", $videoIds), $max);
        return array_map(function ($item) {
            return new VideoObject($item);
        }, $response["items"]);
    }

    private function fetchVideosRecursive($videos, int $max)
    {
        return $this->videos->listVideos(
            'snippet,liveStreamingDetails,statistics,contentDetails',
            array(
                'id' => $videos,
                'maxResults' => $max
            )
        );
    }
}
