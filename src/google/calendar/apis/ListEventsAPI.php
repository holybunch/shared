<?php

namespace holybunch\shared\google\calendar\apis;

use DateTime;
use DateTimeZone;
use Exception;
use Google\Service\Calendar;
use Google_Client;
use holybunch\shared\exceptions\SharedException;
use holybunch\shared\google\youtube\objects\EventObj;

class ListEventsAPI extends Calendar
{
    /**
     * Constructs a new Service object for interacting with the YouTube Data API using
     * the provided Google API client instance.
     *
     * @param Google_Client $client An authorized Google API client instance.
     */
    public function __construct(Google_Client $client)
    {
        parent::__construct($client);
    }

    /**
     * @param string $calendarId
     * @param string $eventName
     * @param string $timezone
     * @return EventObj|null Array of event details or null if no events found
     */
    public function events(string $calendarId, string $eventName, string $timezone): ?EventObj
    {
        try {
            $now = new DateTime('now');
            $dayOfWeek = (int)$now->format('N'); // Get the current day of the week (1 = Monday, 7 = Sunday)

            $friday = clone $now;
            if ($dayOfWeek <= 5) { // Monday to Friday
                $friday->modify('next saturday');
            } else { // Saturday or Sunday
                $friday->modify('today');
            }

            $sunday = clone $now;
            $sunday->modify('next monday');

            $fromFormatted = $friday->format(DateTime::RFC3339);
            $toFormatted = $sunday->format(DateTime::RFC3339);

            // List events within the specified time range and containing "Interested" in the title
            $optParams = array(
                'timeMin' => $fromFormatted,
                'timeMax' => $toFormatted,
                'singleEvents' => true,
                'orderBy' => 'startTime',
                'q' => $eventName,
                'eventTypes' => ["default"]
            );

            $events = $this->events->listEvents($calendarId, $optParams);

            foreach ($events->getItems() as $event) {
                $eventStart = $event->getStart();
                $eventStartDateTime = new DateTime($eventStart->dateTime);
                $eventStartDateTime->setTimezone(new DateTimeZone($timezone)); // Replace with your target timezone

                return new EventObj($eventStartDateTime, $event);
            }
            return null;
        } catch (Exception $e) {
            throw new SharedException($e);
        }
    }
}
