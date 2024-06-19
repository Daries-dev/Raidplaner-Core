<?php

namespace rp\system\cache\runtime;

use rp\data\event\EventList;
use wcf\system\cache\runtime\AbstractRuntimeCache;

/**
 * Runtime cache implementation for events.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @method  Event[] getCachedObjects()
 * @method  Event   getObject($objectID)
 * @method  Event[] getObjects(array $objectIDs)
 */
final class EventRuntimeCache extends AbstractRuntimeCache
{
    /**
     * @inheritDoc
     */
    protected $listClassName = EventList::class;
}
