<?php

namespace rp\system\event\command;

use rp\data\event\Event;
use rp\data\event\EventAction;
use rp\event\event\EventEnabledDisabled;
use rp\system\log\modification\EventModificationLogHandler;
use wcf\system\event\EventHandler;

/**
 * Enable/Disable a event.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
final class EnableDisableEvent
{
    public function __construct(
        private readonly Event $event,
        private readonly bool $isEnabled,
    ) {}

    public function __invoke(): void
    {
        $action = new EventAction(
            [$this->event],
            'update',
            [
                'data' => [
                    'isDisabled' => $this->isEnabled ? 1 : 0,
                ],
                'noLog' => true,
            ]
        );
        $action->executeAction();

        if ($this->isEnabled) {
            EventModificationLogHandler::getInstance()->disable($this->event);
        } else {
            EventModificationLogHandler::getInstance()->enable($this->event);
        }

        $event = new EventEnabledDisabled(new Event($this->event->eventID), !$this->isEnabled);
        EventHandler::getInstance()->fire($event);
    }
}
