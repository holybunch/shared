<?php

declare(strict_types=1);

namespace holybunch\shared\tests\google\calendar\apis;

use DateTime;
use Exception;
use Google\Service\Calendar\Event;
use Google\Service\Calendar\EventDateTime;
use Google\Service\Calendar\Resource\Events;
use Google_Client;
use Google\Service\Calendar\Events as EventModel;
use holybunch\shared\exceptions\SharedException;
use holybunch\shared\google\calendar\apis\EventsAPI;
use holybunch\shared\tests\BaseTest;
use PHPUnit\Framework\MockObject\MockObject;

final class EventsAPITest extends BaseTest
{
    private EventsAPI $eventsAPI;
    private Events&MockObject $eventsMock;

    public function setUp(): void
    {
        $this->eventsAPI = new EventsAPI(new Google_Client());
        $this->eventsMock = $this->getMockBuilder(Events::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testEventsHappy(): void
    {
        $this->eventsMock->expects($this->once())
            ->method('listEvents')
            ->with(
                $this->equalTo("calendar-id"),
                $this->callback(function ($array) {
                    return !empty($array) && $array['q'] == "event-name";
                })
            )
            ->willReturn($this->demoEvents());

        $this->eventsAPI->events = $this->eventsMock;

        $result = $this->eventsAPI->event("calendar-id", "event-name", 'Europe/London');
        $this->assertNotNull($result);
        $this->assertEquals("17 Mar 2024", $result->getDate());
        $this->assertEquals("09:00", $result->getTime());
        $this->assertEquals("Sunday", $result->getDayOfWeek());
        $this->assertEquals("event-name", $result->getTitle());
        $this->assertEquals("https://us02web.zoom.us/j/123456789?pwd=code1", $result->getUrl());
    }

    public function testEventsUnexpectedUrlHappy(): void
    {
        $this->eventsMock->expects($this->once())
            ->method('listEvents')
            ->with(
                $this->equalTo("calendar-id"),
                $this->callback(function ($array) {
                    return !empty($array) && $array['q'] == "event-name";
                })
            )
            ->willReturn($this->demoEvents(false, "unexpected-url"));

        $this->eventsAPI->events = $this->eventsMock;

        $result = $this->eventsAPI->event("calendar-id", "event-name", 'Europe/London');
        $this->assertNotNull($result);
        $this->assertEquals("https://us02web.zoom.us/j/authz-error", $result->getUrl());
    }

    public function testEventsNullHappy(): void
    {
        $this->eventsMock->expects($this->once())
            ->method('listEvents')
            ->with(
                $this->equalTo("calendar-id"),
                $this->callback(function ($array) {
                    return !empty($array) && $array['q'] == "event-name";
                })
            )
            ->willReturn($this->demoEvents(true));

        $this->eventsAPI->events = $this->eventsMock;

        $this->assertNull($this->eventsAPI->event("calendar-id", "event-name", 'Europe/London'));
    }

    public function testEventsFailed(): void
    {
        $this->eventsMock->expects($this->once())
            ->method('listEvents')
            ->with(
                $this->equalTo("calendar-id"),
                $this->callback(function ($array) {
                    return !empty($array) && $array['q'] == "event-name";
                })
            )
            ->willThrowException(new Exception("error ocurred"));

        $this->eventsAPI->events = $this->eventsMock;

        $this->expectException(SharedException::class);
        $this->expectExceptionMessage("error ocurred");
        $this->eventsAPI->event("calendar-id", "event-name", 'Europe/London');
    }

    private function demoEvents(bool $doNull = false, string $desc = "Zoom: https://us02web.zoom.us/j/123456789?pwd=code"): EventModel
    {
        $eventModel = new EventModel();
        $events = [];

        if (!$doNull) {
            $startTimes = [
                "2024-03-17T09:00:00", // Example start time for event 1
                "2024-03-20T10:00:00", // Example start time for event 2
            ];

            foreach ($startTimes as $i => $startTime) {
                $ev = new Event();
                $startDateTime = new DateTime($startTime);
                $ev->setStart(new EventDateTime());
                $ev->getStart()->setDateTime($startDateTime->format(DATE_ATOM));
                $ev->setSummary("event-name");
                $ev->setDescription($desc . ($i + 1));
                $events[] = $ev;
            }
        }

        $eventModel->setItems($events);
        return $eventModel;
    }
}
