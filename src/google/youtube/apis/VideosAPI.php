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
    private const MAX_RESULT = 45;

    /** @var VideoObject[] */
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

    /**
     * Retrieves video objects based on provided video IDs.
     *
     * @param string[] $videoIds Array containing video IDs to retrieve video objects for.
     * @param int $max Maximum number of video objects to retrieve (default is 50).
     * @return VideoObject[] Array containing the retrieved video objects.
     * @throws SharedException If an exception occurs during the retrieval process.
     */
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

    /**
    * Processes video IDs to retrieve video details.
    *
    * @param string[] $videoIds Array containing video IDs to retrieve details for.
    * @param int $max Maximum number of video objects to retrieve.
    */
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
