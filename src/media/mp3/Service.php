<?php

namespace holybunch\shared\media\mp3;

use Exception;
use holybunch\shared\exceptions\SharedException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use UnexpectedValueException;

class Service
{
    /** @var string[] */
    private array $exclusions;

    /** @return string[] */
    public function exclusions(): array
    {
        return $this->exclusions;
    }

    private string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * @return string[]
     */
    public function collectionSongs(string $collection): array
    {
        try {
            $songs = [];
            $this->exclusions = [];
            $rdi = new RecursiveDirectoryIterator($this->path . $collection);
            foreach (new RecursiveIteratorIterator($rdi) as $file) {
                if ($file instanceof SplFileInfo) {
                    $this->processMusicFile($file, $collection, $songs);
                }
            }
            return $songs;
        } catch (UnexpectedValueException $e) {
            throw new SharedException($e);
        }
    }

    /**
     * @param string[] $songs
     */
    private function processMusicFile(SplFileInfo $file, string $collection, array &$songs): void
    {
        try {
            if ($file->isFile()) {
                $songs[] = Entity::parse($file->getPathname());
            }
        } catch (Exception $e) {
            $this->exclusions[] = "error for collection '$collection' and file '$file' occurred: " . $e->getMessage();
        }
    }
}
