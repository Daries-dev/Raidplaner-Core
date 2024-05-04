<?php

namespace rp\data\point\account;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of point accounts.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 *
 * @method  PointAccount    current()
 * @method  PointAccount[]  getObjects()
 * @method  PointAccount|null   search($objectID)
 * @property    PointAccount[]  $objects
 */
class PointAccountList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = PointAccount::class;
}
