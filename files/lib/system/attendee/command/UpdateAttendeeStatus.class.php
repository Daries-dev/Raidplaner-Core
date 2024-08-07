<?php

namespace rp\system\attendee\command;

use rp\data\event\raid\attendee\EventRaidAttendee;
use rp\data\event\raid\attendee\EventRaidAttendeeAction;
use rp\event\attendee\AttendeeUpdated;
use wcf\system\event\EventHandler;

/**
 * Updates a attendee.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
final class UpdateAttendeeStatus
{
    public function __construct(
        private readonly EventRaidAttendee $attendee,
        private readonly int $roleID,
        private readonly string $status,
    ) {
    }

    public function __invoke(): void
    {
        $data = [
            'roleID' => $this->roleID,
            'status' => $this->status,
        ];

        $action = new EventRaidAttendeeAction([$this->attendee], 'update', [
            'data' => $data,
        ]);
        $action->executeAction();

        $event = new AttendeeUpdated(new EventRaidAttendee($this->attendee->attendeeID));
        EventHandler::getInstance()->fire($event);
    }
}
