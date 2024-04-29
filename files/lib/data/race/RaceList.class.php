<?php

namespace rp\data\race;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of races.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 *
 * @method  Race        current()
 * @method  Race[]      getObjects()
 * @method  Race|null   search($objectID)
 * @property    Race[]  $objects
 */
class RaceList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = Race::class;
}
