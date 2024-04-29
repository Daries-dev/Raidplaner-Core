<?php

namespace rp\data\faction;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of factions.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 *
 * @method  Faction     current()
 * @method  Faction[]   getObjects()
 * @method  Faction|null    search($objectID)
 * @property    Faction[]   $objects
 */
class FactionList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = Faction::class;
}
