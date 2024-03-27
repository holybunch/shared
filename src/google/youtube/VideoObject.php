<?php

namespace holybunch\shared\google\youtube;

use DateInterval;
use DateTime;

class VideoObject
{
    private string $id;
    private string $title;
    private string $thumbnail;
    private string $publishedAt;
    private string $likeCount;
    private string $viewCount;
    private string $duration;

    public function __construct(array $apiVideoItem)
    {
        $start = new DateTime('@0'); // Unix epoch
        $start->add(new DateInterval($apiVideoItem['contentDetails']['duration']));
        $this->id = $apiVideoItem['id'];
        $this->title = $apiVideoItem['snippet']['title'];
        $this->thumbnail = $apiVideoItem['snippet']['thumbnails']['medium']['url'];
        $this->publishedAt  = $this->formatDate($apiVideoItem['snippet']['publishedAt']);
        $this->likeCount = $apiVideoItem['statistics']['likeCount'];
        $this->viewCount = $apiVideoItem['statistics']['viewCount'];
        $this->duration = $start->format('H:i:s');
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getThumbnail(): string
    {
        return $this->thumbnail;
    }

    public function getPublishedAt(): string
    {
        return $this->publishedAt;
    }

    public function getLikeCount(): string
    {
        return $this->likeCount;
    }

    public function getViewCount(): string
    {
        return $this->viewCount;
    }

    public function getDuration(): string
    {
        return $this->duration;
    }

    private function formatDate($input): string
    {
        if ($input != null) {
            $date = new DateTime($input);
            return $date->format('Y/m/d h:m:s');
        }
        return null;
    }
}