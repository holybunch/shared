<?php

namespace holybunch\shared\google\youtube\objects;

use DateInterval;
use DateTime;
use Google\Service\YouTube\Video;

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
    private ?string $publishedAt;
    private string $likeCount;
    private string $viewCount;
    private string $duration;

    /**
     * Constructs a VideoObject instance from the provided YouTube API video item array.
     *
     * @param Video $apiVideoItem The google object containing information about the video
     *              item from the YouTube API response.
     */
    public function __construct(Video $apiVideoItem)
    {
        $snippet = $apiVideoItem->getSnippet();

        $start = new DateTime('@0'); // Unix epoch
        $start->add(new DateInterval($apiVideoItem->getContentDetails()->getDuration()));
        $this->duration = $start->format('H:i:s');

        $this->id = $apiVideoItem->getId();
        $this->title = $snippet->getTitle();
        $this->thumbnail = $snippet->getThumbnails()->getMedium()->getUrl();
        $this->publishedAt = $this->formatDate($snippet->getPublishedAt());
        $this->likeCount = $apiVideoItem->getStatistics()->getLikeCount();
        $this->viewCount = $apiVideoItem->getStatistics()->getViewCount();
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
     * @return string|null The published date of the video or null
     */
    public function getPublishedAt(): ?string
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
     * @param string|null $input The input date string
     * @return string|null The formatted date string, or null if input is null
     */
    private function formatDate(?string $input): ?string
    {
        if ($input != null) {
            $date = new DateTime($input);
            return $date->format('Y/m/d h:m:s');
        }
        return null;
    }
}
