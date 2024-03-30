<?php

namespace holybunch\shared\google\youtube\objects;

use DateTime;
use Google\Service\Calendar\Event;

class EventObj
{
    private string $date = '';
    private string $time = '';
    private string $dayOfWeek = '';
    private string $title = '';
    private string $url = '';

    public function __construct(DateTime $eventStartDateTime, Event $event)
    {
        $this->date = $eventStartDateTime->format('d M Y');
        $this->time = $eventStartDateTime->format('H:i');
        $this->dayOfWeek = $eventStartDateTime->format('l');
        $this->title = $event->getSummary();
        $this->url = $this->parseUrl($event->getDescription());
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getTime(): string
    {
        return $this->time;
    }

    public function getDayOfWeek(): string
    {
        return $this->dayOfWeek;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    private function parseUrl(string $description): string
    {
        $regexes = array(
            '/(?<=href\="|\')([^"\']+)(?="|\')/m',
            '/https:([^"\']+)\?pwd=(\w+)/m'
        );

        foreach ($regexes as $regex) {
            preg_match_all($regex, $description, $matches);
            foreach ($matches as $match) {
                foreach ($match as $m) {
                    if (strpos($m, ".zoom.") && strpos($m, "?pwd")) {
                        return $m;
                    }
                }
            }
        }

        return "https://us02web.zoom.us/j/authz-error";
    }
}
