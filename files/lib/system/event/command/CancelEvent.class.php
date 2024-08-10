<?php

namespace rp\system\event\command;

use rp\data\event\Event;
use rp\data\event\EventAction;
use rp\event\event\EventCanceled;
use wcf\system\event\EventHandler;

/**
 * Cancel a event.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
final class CancelEvent
{
    public function __construct(
        private readonly Event $event,
    ) {}

    public function __invoke(): void
    {
        $action = new EventAction(
            [$this->event],
            'update',
            [
                'data' => [
                    'isCanceled' => 1,
                ],
            ]
        );
        $action->executeAction();

        $event = new EventCanceled(new Event($this->event->eventID));
        EventHandler::getInstance()->fire($event);
    }
}