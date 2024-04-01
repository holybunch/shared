<?php

namespace holybunch\shared\google\youtube\objects;

use Google\Service\YouTube\Playlist;

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
    private string $itemCount;

    /**
     * Initializes the PlaylistObject instance with data from the YouTube API playlist item.
     *
     * @param Playlist $apiPlayListItem The google object containing information about the playlist item
     *                 from the YouTube API response.
     */
    public function __construct(Playlist $apiPlayListItem)
    {
        $snippet = $apiPlayListItem->getSnippet();
        $this->id = $apiPlayListItem->getId();
        $this->title = $snippet->getTitle();
        $this->description = $snippet->getDescription();
        $this->thumbnail = $snippet->getThumbnails()->getMedium()->getUrl();
        $this->itemCount = $apiPlayListItem->getContentDetails()->getItemCount();
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
     * @return string The number of items in the playlist.
     */
    public function getItemCount(): string
    {
        return $this->itemCount;
    }
}
