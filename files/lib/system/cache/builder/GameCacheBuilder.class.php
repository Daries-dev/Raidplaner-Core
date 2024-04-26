<?php

namespace rp\system\cache\builder;

use rp\data\game\GameList;
use wcf\system\cache\builder\AbstractCacheBuilder;

/**
 * Caches game information.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Free License <https://daries.dev/en/license-for-free-plugins>
 */
final class GameCacheBuilder extends AbstractCacheBuilder
{
    /**
     * @inheritDoc
     */
    public function rebuild(array $parameters): array
    {
        $gameList = new GameList();
        $gameList->readObjects();
        return $gameList->getObjects();
    }
}
