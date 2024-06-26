<?php

namespace rp\system\calendar;

use rp\data\event\ViewableEvent;
use wcf\util\DateUtil;

/**
 * Represents a day in the calendar and manages the associated events.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class Day
{
    /**
     * day object
     */
    private \DateTimeImmutable $dayObj;

    /**
     * events
     * @var DayEvent[]
     */
    private array $events = [];

/**
 * Creates a new Day object for the specified date.
 */
    public function __construct(
        private readonly int $year,
        private readonly int $month,
        private readonly int $day
    ) {
        $this->dayObj = new \DateTimeImmutable("{$year}-{$month}-{$day}");
    }

    /**
     * Returns the date of the day as a string in the format 'Y-m-d'.
     */
    public function __toString(): string
    {
        return $this->dayObj->format('Y-m-d');
    }

    /**
     * Adds an event for this day.
     */
    public function addEvent(DayEvent $dayEvent): void
    {
        $this->events[] = $dayEvent;
    }

    /**
     * Returns the day.
     */
    public function getDay(): int
    {
        return $this->day;
    }

    /**
     * Returns events for this day.
     */
    public function getEvents(): array
    {
        return $this->events;
    }
}
