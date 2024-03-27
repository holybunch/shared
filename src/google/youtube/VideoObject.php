<?php

namespace holybunch\shared\google\youtube;

use DateInterval;
use DateTime;

/**
 * Represents a YouTube video object retrieved from the API.
 * This class encapsulates the properties of a YouTube video item.
 *
 * @author holybunch
 */
class VideoObject
{
    private string $id;
    private string $title;
    private string $thumbnail;
    private string $publishedAt;
    private string $likeCount;
    private string $viewCount;
    private string $duration;

    /**
     * Constructor to initialize a VideoObject instance from YouTube API video item data.
     *
     * @param array $apiVideoItem The YouTube API video item data
     */
    public function __construct(array $apiVideoItem)
    {
        $start = new DateTime('@0'); // Unix epoch
        $start->add(new DateInterval($apiVideoItem['contentDetails']['duration']));
        
        $this->id = $apiVideoItem['id'];
        $this->title = $apiVideoItem['snippet']['title'];
        $this->thumbnail = $apiVideoItem['snippet']['thumbnails']['medium']['url'];
        $this->publishedAt = $this->formatDate($apiVideoItem['snippet']['publishedAt']);
        $this->likeCount = $apiVideoItem['statistics']['likeCount'];
        $this->viewCount = $apiVideoItem['statistics']['viewCount'];
        $this->duration = $start->format('H:i:s');
    }

    /**
     * Get the video ID.
     *
     * @return string The video ID
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Get the video title.
     *
     * @return string The video title
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Get the URL of the video thumbnail.
     *
     * @return string The URL of the video thumbnail
     */
    public function getThumbnail(): string
    {
        return $this->thumbnail;
    }

    /**
     * Get the published date of the video.
     *
     * @return string The published date of the video
     */
    public function getPublishedAt(): string
    {
        return $this->publishedAt;
    }

    /**
     * Get the number of likes on the video.
     *
     * @return string The number of likes on the video
     */
    public function getLikeCount(): string
    {
        return $this->likeCount;
    }

    /**
     * Get the number of views on the video.
     *
     * @return string The number of views on the video
     */
    public function getViewCount(): string
    {
        return $this->viewCount;
    }

    /**
     * Get the duration of the video.
     *
     * @return string The duration of the video
     */
    public function getDuration(): string
    {
        return $this->duration;
    }

    /**
     * Format the date string to a specific format.
     *
     * @param mixed $input The input date string
     * @return string|null The formatted date string, or null if input is null
     */
    private function formatDate($input): ?string
    {
        if ($input != null) {
            $date = new DateTime($input);
            return $date->format('Y/m/d h:m:s');
        }
        return null;
    }
}
