<?php

namespace holybunch\shared\google\calendar;

use holybunch\shared\google\ClientBase;
use Google\Service\Calendar;

/**
 * Google Calendar API client class.
 *
 * @author holybunch
 */
class Client extends ClientBase
{
    /**
     * Constructs a new Client object for interacting with the Google Calendar API.
     */
    public function __construct()
    {
        parent::__construct([
            Calendar::CALENDAR,
            Calendar::CALENDAR_EVENTS,
            Calendar::CALENDAR_EVENTS_READONLY,
            Calendar::CALENDAR_READONLY,
            Calendar::CALENDAR_SETTINGS_READONLY
        ]);
        $this->service = "CALENDAR";
    }
}
