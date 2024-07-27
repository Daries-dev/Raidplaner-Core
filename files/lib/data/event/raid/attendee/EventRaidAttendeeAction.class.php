<?php

namespace rp\data\event\raid\attendee;

use wcf\data\AbstractDatabaseObjectAction;

/**
 * Executes event raid attendee related actions.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @method  EventRaidAttendeeEditor[]   getObjects()
 * @method  EventRaidAttendeeEditor     getSingleObject()
 */
class EventRaidAttendeeAction extends AbstractDatabaseObjectAction
{
    /**
     * @inheritDoc
     */
    public function create(): EventRaidAttendee
    {
        $this->parameters['data']['created'] = TIME_NOW;

        return parent::create();
    }
}
