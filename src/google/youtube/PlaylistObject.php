<?php

namespace holybunch\shared\google\youtube;

class PlaylistObject
{
    private string $id;
    private string $title;
    private string $description;
    private string $thumbnail;
    private string $itemCount;

    public function __construct($apiPlayListItem)
    {
        $this->id = $apiPlayListItem["id"];
        $this->title = $apiPlayListItem["snippet"]["title"];
        $this->description = $apiPlayListItem["snippet"]["description"];
        $this->thumbnail = $apiPlayListItem["snippet"]["thumbnails"]["medium"]["url"]; //320/180
        $this->itemCount = $apiPlayListItem["contentDetails"]["itemCount"];
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getThumbnail()
    {
        return $this->thumbnail;
    }

    public function getItemCount()
    {
        return $this->itemCount;
    }
}
