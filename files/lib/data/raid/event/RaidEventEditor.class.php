<?php

namespace rp\data\raid\event;

use rp\system\cache\builder\PointAccountCacheBuilder;
use rp\system\cache\builder\RaidEventCacheBuilder;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;

/**
 * Provides functions to edit raid event.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @method static   RaidEvent       create(array $parameters = [])
 * @method  RaidEvent       getDecoratedObject()
 * @mixin   RaidEvent
 */
class RaidEventEditor extends DatabaseObjectEditor implements IEditableCachedObject
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = RaidEvent::class;

    /**
     * @inheritDoc
     */
    public static function resetCache(): void
    {
        RaidEventCacheBuilder::getInstance()->reset();
        PointAccountCacheBuilder::getInstance()->reset();
    }
}
