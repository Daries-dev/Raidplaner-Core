<?php

namespace rp\system\cache\runtime;

use rp\data\character\CharacterProfileList;
use wcf\system\cache\runtime\AbstractRuntimeCache;

/**
 * Runtime cache implementation for character profiles.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Free License <https://daries.dev/en/license-for-free-plugins>
 * 
 * @method  CharacterProfile[]  getCachedObjects()
 * @method  CharacterProfile    getObject($objectID)
 * @method  CharacterProfile[]  getObjects(array $objectIDs)
 */
final class CharacterProfileRuntimeCache extends AbstractRuntimeCache
{
    /**
     * @inheritDoc
     */
    protected $listClassName = CharacterProfileList::class;
}
