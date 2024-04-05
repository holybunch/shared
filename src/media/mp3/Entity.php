<?php

namespace holybunch\shared\media\mp3;

use wapmorgan\Mp3Info\Mp3Info;

class Entity
{
    private const NA = "N/A";

    private string $fileName;
    private string $title;
    private string $artist;
    private string $album;
    private string $year;
    private string $comment;
    private string $filesize;

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getArtist(): string
    {
        return $this->artist;
    }

    public function getAlbum(): string
    {
        return $this->album;
    }

    public function getYear(): string
    {
        return $this->year;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function getFilesize(): string
    {
        return $this->filesize;
    }

    private function __construct()
    {
    }

    public static function parse(string $path): self
    {
        $entity = new self();

        $audio = new Mp3Info($path, true);
        $entity->fileName = basename($path);
        $entity->title =  array_key_exists('song', $audio->tags) ? $audio->tags['song'] : self::NA;
        $entity->artist = array_key_exists('artist', $audio->tags) ? $audio->tags['artist'] : self::NA;
        $entity->album = array_key_exists('album', $audio->tags) ? $audio->tags['album'] : self::NA;
        $entity->year = array_key_exists('year', $audio->tags) ? $audio->tags['year'] : self::NA;
        $entity->comment = array_key_exists('comment', $audio->tags) ? $audio->tags['comment'] : self::NA;
        $entity->filesize = number_format(filesize($path) / 1048576, 1, ',', '');

        return $entity;
    }
}
