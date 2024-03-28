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
    private ?string $publishedAt;
    private int $likeCount;
    private int $viewCount;
    private string $duration;

    /**
     * Constructs a VideoObject instance from the provided YouTube API video item array.
     *
     * @param array{
     *      id: string,
     *      snippet: array{
     *          title: string,
     *          publishedAt: string|null,
     *          thumbnails: array{
     *              medium: array{
     *                  url: string,
     *              },
     *          },
     *      },
     *      statistics: array{
     *          likeCount: int,
     *          viewCount: int,
     *      },
     *      contentDetails: array{
     *          duration: string,
     *      }
     * } $apiVideoItem The array containing information about the video item from the YouTube API response.
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
     * @return string|null The published date of the video or null
     */
    public function getPublishedAt(): ?string
    {
        return $this->publishedAt;
    }

    /**
     * Get the number of likes on the video.
     *
     * @return int The number of likes on the video
     */
    public function getLikeCount(): int
    {
        return $this->likeCount;
    }

    /**
     * Get the number of views on the video.
     *
     * @return int The number of views on the video
     */
    public function getViewCount(): int
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
