<?php

namespace rp\system\cache\builder;

use rp\data\character\CharacterProfileList;
use wcf\system\cache\builder\AbstractCacheBuilder;

/**
 * Caches my characters.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class MyCharactersCacheBuilder extends AbstractCacheBuilder
{
    /**
     * @inheritDoc
     */
    protected $maxLifetime = 300;

    /**
     * @inheritDoc
     */
    protected function rebuild(array $parameters): array
    {
        $data = [];

        $list = new CharacterProfileList();
        $list->getConditionBuilder()->add('userID = ?', [$parameters['userID']]);
        $list->getConditionBuilder()->add('isDisabled = ?', [0]);
        $list->sqlOrderBy = 'characterName ASC';
        $list->readObjects();

        foreach ($list as $character) {
            $data[$character->gameID] ??= [];
            $data[$character->gameID][] = $character;
        }

        return $data;
    }
}
