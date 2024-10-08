<?php

namespace rp\data\item;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of items.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 *
 * @method  Item    current()
 * @method  Item[]  getObjects()
 * @method  Item|null   search($objectID)
 * @property    Item[]  $objects
 */
class ItemList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = Item::class;
}
