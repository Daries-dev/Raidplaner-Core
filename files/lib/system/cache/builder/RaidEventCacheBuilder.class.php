<?php

namespace rp\system\cache\builder;

use rp\data\raid\event\I18nRaidEventList;
use wcf\system\cache\builder\AbstractCacheBuilder;

/**
 * Caches the raid event.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class RaidEventCacheBuilder extends AbstractCacheBuilder
{
    /**
     * @inheritDoc
     */
    protected function rebuild(array $parameters): array
    {
        $list = new I18nRaidEventList();
        $list->readObjects();
        return $list->getObjects();
    }
}
