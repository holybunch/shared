<?php

namespace holybunch\shared\media\mp3;

use Exception;
use holybunch\shared\exceptions\NotFoundException;
use holybunch\shared\exceptions\SharedException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use UnexpectedValueException;

/**
 * Class Service
 * Service class for managing MP3 media files.
 */
class Service
{
    /** @var string[] $exclusions Array to store exclusion messages. */
    private array $exclusions;

    /**
     * Returns the exclusions array.
     *
     * @return string[] The array of exclusion messages.
     */
    public function exclusions(): array
    {
        return $this->exclusions;
    }

    /** @var string $path The base path for the MP3 media files. */
    private string $path;

    /**
     * Constructor for Service class.
     *
     * @param string $path The base path for the MP3 media files.
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * Retrieves the songs from the specified collection.
     *
     * @param string $collection The name of the collection to retrieve songs from.
     * @return Entity[] An array of Entity objects representing the songs in the collection.
     * @throws SharedException If an error occurs during song retrieval.
     */
    public function songs(string $collection): array
    {
        try {
            $songs = [];
            $this->exclusions = [];
            $rdi = new RecursiveDirectoryIterator($this->path . $collection);
            foreach (new RecursiveIteratorIterator($rdi) as $file) {
                if ($file instanceof SplFileInfo) {
                    $this->processEntity($file, $songs);
                }
            }
            return $songs;
        } catch (UnexpectedValueException $e) {
            throw new SharedException($e);
        }
    }

    /**
     * Retrieves the collections available in the MP3 media.
     *
     * @return string[] An array of collection names.
     * @throws NotFoundException If an error occurs during collection retrieval.
     */
    public function collections(): array
    {
        $subfolders = [];
        $folders = glob(rtrim($this->path, '/') . '/*', GLOB_ONLYDIR);
        if (!$folders) {
            throw new NotFoundException("An error occurred during the retrieval of collections.");
        }
        foreach ($folders as $folder) {
            $subfolders[] = basename($folder);
        }
        return $subfolders;
    }

    /**
     * Processes the given file as an Entity object and adds it to the songs array.
     *
     * @param SplFileInfo $file       The file to process.
     * @param Entity[]    $songs      The array of songs to add the Entity object to.
     */
    private function processEntity(SplFileInfo $file, array &$songs): void
    {
        try {
            if ($file->isFile()) {
                $songs[] = Entity::parse($file->getPathname());
            }
        } catch (Exception $e) {
            $this->exclusions[] = "error for the file '$file' occurred: " . $e->getMessage();
        }
    }
}
