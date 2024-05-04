<?php

namespace rp\data\raid\event;

use wcf\data\I18nDatabaseObjectList;

/**
 * I18n implementation of raid event list.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 *
 * @method  RaidEvent       current()
 * @method  RaidEvent[]     getObjects()
 * @method  RaidEvent|null      search($objectID)
 * @property    RaidEvent[]     $objects
 */
class I18nRaidEventList extends I18nDatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $i18nFields = ['title' => 'titleI18n'];

    /**
     * @inheritDoc
     */
    public $className = RaidEvent::class;
}
