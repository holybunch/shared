<?php

namespace holybunch\shared\google\youtube;

/**
 * Represents a YouTube playlist object retrieved from the API.
 * This class encapsulates the properties of a YouTube playlist item.
 *
 * @author holybunch
 */
class PlaylistObject
{
    private string $id;
    private string $title;
    private string $description;
    private string $thumbnail;
    private int $itemCount;

    /**
     * Initializes the PlaylistObject instance with data from the YouTube API playlist item.
     *
     * @param array{
     *      id: string,
     *      snippet: array{
     *          title: string,
     *          description: string,
     *          thumbnails: array{
     *              medium: array{
     *                  url: string,
     *              },
     *          },
     *      },
     *      contentDetails: array{
     *          itemCount: int,
     *      },
     * } $apiPlayListItem The array containing information about the playlist item from the YouTube API response.
     */
    public function __construct(array $apiPlayListItem)
    {
        $this->id = $apiPlayListItem["id"];
        $this->title = $apiPlayListItem["snippet"]["title"];
        $this->description = $apiPlayListItem["snippet"]["description"];
        $this->thumbnail = $apiPlayListItem["snippet"]["thumbnails"]["medium"]["url"]; //320/180
        $this->itemCount = $apiPlayListItem["contentDetails"]["itemCount"];
    }

    /**
     * Gets the ID of the playlist.
     *
     * @return string The ID of the playlist.
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Gets the title of the playlist.
     *
     * @return string The title of the playlist.
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Gets the description of the playlist.
     *
     * @return string The description of the playlist.
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Gets the URL of the thumbnail image for the playlist.
     *
     * @return string The URL of the thumbnail image for the playlist.
     */
    public function getThumbnail(): string
    {
        return $this->thumbnail;
    }

    /**
     * Gets the number of items in the playlist.
     *
     * @return int The number of items in the playlist.
     */
    public function getItemCount(): int
    {
        return $this->itemCount;
    }
}
