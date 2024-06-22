<?php

namespace holybunch\shared\google\calendar\apis;

use DateTime;
use DateTimeZone;
use Exception;
use Google\Service\Calendar;
use Google_Client;
use holybunch\shared\exceptions\SharedException;
use holybunch\shared\google\calendar\objects\EventObj;

/**
 * Class EventsAPI
 *
 * This class provides methods for interacting with Google Calendar events.
 */
class EventsAPI extends Calendar
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
     * Retrieves an event from the specified calendar.
     *
     * @param string $calendarId The ID of the calendar.
     * @param string $eventName The name of the event to search for.
     * @param string $timezone The timezone to use for date/time conversions.
     * @return EventObj|null An EventObj representing the event found, or null if no events are found.
     * @throws SharedException If an error occurs during the operation.
     */
    public function event(string $calendarId, string $eventName, string $timezone): ?EventObj
    {
        try {
            $optParams = array(
                'timeMin' => $this->fromFormatted(),
                'timeMax' => $this->toFormatted(),
                'singleEvents' => true,
                'orderBy' => 'startTime',
                'q' => $eventName,
                'eventTypes' => ["default"]
            );

            $events = $this->events->listEvents($calendarId, $optParams);

            foreach ($events->getItems() as $event) {
                $eventStart = $event->getStart();
                $eventStartDateTime = new DateTime($eventStart->dateTime);
                $eventStartDateTime->setTimezone(new DateTimeZone($timezone));

                return new EventObj($eventStartDateTime, $event);
            }
            return null;
        } catch (Exception $e) {
            throw new SharedException($e);
        }
    }

    /**
     * Formats the current date/time as the starting point for event retrieval.
     *
     * @return string The formatted date/time string.
     */
    private function fromFormatted(): string
    {
        $now = new DateTime('now');
        $now->setISODate($now->format('Y'), $now->format('W'));
        $now->modify('+4 days');
        $fridayOfWeek = $now->format(DateTime::RFC3339);
        return $fridayOfWeek;
    }

    /**
     * Formats the current date/time as the ending point for event retrieval.
     *
     * @return string The formatted date/time string.
     */
    private function toFormatted(): string
    {
        $now = new DateTime('now');
        $now->modify('next monday');
        return $now->format(DateTime::RFC3339);
    }
}
