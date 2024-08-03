<?php

namespace rp\event\attendee;

use rp\data\event\raid\attendee\EventRaidAttendee;
use wcf\event\IPsr14Event;

/**
 * Indicates that multiple attendees have been deleted.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @property-read EventRaidAttendee[] $attendees
 */
final class AttendeesDeleted implements IPsr14Event
{
    public function __construct(
        public readonly array $attendees,
    ) {
    }
}
