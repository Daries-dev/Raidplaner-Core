<?php

namespace rp\data\event\raid\attendee;

use wcf\data\DatabaseObjectEditor;

/**
 * Provides functions to edit event raid attendee.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @method static   EventRaidAttendee   create(array $parameters = [])
 * @method  EventRaidAttendee       getDecoratedObject()
 * @mixin   EventRaidAttendee
 */
class EventRaidAttendeeEditor extends DatabaseObjectEditor
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = EventRaidAttendee::class;
}
