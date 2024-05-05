<?php

namespace rp\system\calendar;

use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\DateUtil;

/**
 * Represents a calendar for a specific month and year, 
 * allowing users to add events and render the calendar.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class Calendar
{
    /**
     * Store events, indexed by day of the month
     */
    private array $events = [];

    /**
     * The first day of the last month
     */
    private \DateTimeImmutable $firstDayOfLastMonth;

    /**
     * The first day of the current month.
     */
    private \DateTimeImmutable $firstDayOfMonth;

    /**
     * The first day of the next month.
     */
    private \DateTimeImmutable $firstDayOfNextMonth;

    /**
     * The localized name of the month.
     */
    private string $monthName;

    /**
     * Constructor to initialize the Calendar object with the given year and month.
     */
    public function __construct(
        private readonly int $year,
        private readonly int $month
    ) {
        $this->firstDayOfMonth = new \DateTimeImmutable("{$year}-{$month}-01");
        $this->monthName = DateUtil::localizeDate($this->firstDayOfMonth->format('F'), 'F', WCF::getLanguage());

        $this->firstDayOfLastMonth = (clone $this->firstDayOfMonth)->modify('-1 month');
        $this->firstDayOfNextMonth = (clone $this->firstDayOfMonth)->modify('+1 month');
    }

    // TODO Adjust Add Event to the event that is actually transferred.
    /**
     * Adds an event to the calendar.
     */
    public function addEvent($event): void
    {
        $day = DateUtil::getDateTimeByTimestamp($event->startTime)->format('j');
        $this->events[$day][] = $event;

        // Sort the events for this day by start time
        \usort($this->events[$day], static function ($a, $b) {
            return $a->startTime <=> $b->startTime;
        });
    }

    /**
     * Returns the link to the last month.
     */
    public function getLastMonthLink(): string
    {
        return LinkHandler::getInstance()->getLink('Calendar', [
            'application' => 'rp',
            'month' => $this->firstDayOfLastMonth->format('n'),
            'year' => $this->firstDayOfLastMonth->format('Y'),
        ]);
    }

    /**
     * Returns the link to the next month.
     */
    public function getNextMonthLink(): string
    {
        return LinkHandler::getInstance()->getLink('Calendar', [
            'application' => 'rp',
            'month' => $this->firstDayOfNextMonth->format('n'),
            'year' => $this->firstDayOfNextMonth->format('Y'),
        ]);
    }

    /**
     * Renders the calendar and returns this as HTML.
     */
    public function render(): string
    {
        $weekDays = DateUtil::getWeekDays();
        $firstDayOfWeek = (int)$this->firstDayOfMonth->format('N');
        $monthDays = $this->firstDayOfMonth->format('t');

        $days = [];
        foreach ($weekDays as $key => $weekDay) {
            if ($key === $firstDayOfWeek) break;
            $days[] = null;
        }

        for ($i = 1; $i <= $monthDays; $i++) {
            $days[] = $i;
        }

        $lastDayOfWeek = new \DateTimeImmutable("{$this->year}-{$this->month}-{$monthDays}");
        $lastDayOfWeek = (int)$lastDayOfWeek->format('N');
        $isLastDay = false;
        foreach ($weekDays as $key => $weekDay) {
            if ($key === $lastDayOfWeek) {
                $isLastDay = true;
                continue;
            }

            if ($isLastDay) {
                $days[] = null;
            }
        }

        return WCF::getTPL()->fetch('renderCalendar', 'rp', [
            'days' => $days,
            'events' => $this->events,
            'monthName' => $this->monthName,
            'weekDays' => $weekDays,
            'year' => $this->year,
        ], true);
    }
}
