<?php

namespace rp\data\game;

use wcf\data\DatabaseObject;
use wcf\data\ITitledObject;
use wcf\system\WCF;

/**
 * Represents a game.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Free License <https://daries.dev/en/license-for-free-plugins>
 * 
 * @property-read   int $gameID     unique id of the game
 * @property-read   string  $identifier     unique textual identifier of the game
 * @property-read   string  $title      title of the game or name of language item which contains the title
 * @property-read   int $packageID      id of the package which delivers the game
 */
final class Game extends DatabaseObject implements ITitledObject
{
    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return WCF::getLanguage()->get($this->title);
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->getTitle();
    }
}
