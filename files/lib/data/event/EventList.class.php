<?php

namespace rp\data\event;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of events.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 *
 * @method  Event   current()
 * @method  Event[] getObjects()
 * @method  Event|null  search($objectID)
 * @property    Event[] $objects
 */
class EventList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = Event::class;

    /**
     * @inheritDoc
     */
    public $sqlOrderBy = 'event.startTime';

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        parent::__construct();

        $this->conditionBuilder->add('event.gameID = ?', [RP_CURRENT_GAME_ID]);
    }
}
