<?php

namespace rp\system\cache\runtime;

use rp\data\character\CharacterProfile;
use rp\data\character\CharacterProfileList;
use wcf\system\cache\runtime\AbstractRuntimeCache;

/**
 * Runtime cache implementation for character profiles.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
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
