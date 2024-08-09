<?php

namespace rp\event\event;

use rp\data\event\Event;
use wcf\event\IPsr14Event;

/**
 * Indicates that a event has been enabled/disabled.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class EventEnabledDisabled implements IPsr14Event
{
    public function __construct(
        public readonly Event $event,
        public readonly bool $isDisabled,
    ) {}
}
