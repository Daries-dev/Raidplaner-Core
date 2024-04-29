<?php

namespace rp\data\classification;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of classifications.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Free License <https://daries.dev/en/license-for-free-plugins>
 *
 * @method  Classification      current()
 * @method  Classification[]    getObjects()
 * @method  Classification|null     search($objectID)
 * @property    Classification[]    $objects
 */
class ClassificationList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = Classification::class;
}
