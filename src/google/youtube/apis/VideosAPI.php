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
    public const MAX_RESULT = 30;

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
     * @return VideoObject[] Array containing the retrieved video objects.
     * @throws SharedException If an exception occurs during the retrieval process.
     */
    public function videos(array $videoIds): array
    {
        try {
            $this->videoObjects = [];
            foreach (array_chunk($videoIds, self::MAX_RESULT) as $singleArray) {
                $this->processVideos($singleArray);
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
    */
    private function processVideos(array $videoIds): void
    {
        $response = $this->videos->listVideos(
            'snippet,liveStreamingDetails,statistics,contentDetails',
            [
                'id' => implode(",", $videoIds),
                'maxResults' => self::MAX_RESULT
            ]
        );

        foreach ($response["items"] as $item) {
            $this->videoObjects[] = new VideoObject($item);
        }
    }
}
