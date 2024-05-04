<?php

namespace rp\data\raid\event;

use wcf\data\DatabaseObjectList;

/**
 * Provides functions to edit raid event.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 *
 * @method  RaidEvent       current()
 * @method  RaidEvent[]     getObjects()
 * @method  RaidEvent|null      search($objectID)
 * @property    RaidEvent[]     $objects
 */
class RaidEventList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = RaidEvent::class;
}
