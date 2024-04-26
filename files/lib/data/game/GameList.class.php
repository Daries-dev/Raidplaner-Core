<?php

namespace rp\data\game;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of games.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Free License <https://daries.dev/en/license-for-free-plugins>
 *
 * @method  Game        current()
 * @method  Game[]      getObjects()
 * @method  Game|null   search($objectID)
 * @property    Game[]  $objects
 */
class GameList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = Game::class;
}
