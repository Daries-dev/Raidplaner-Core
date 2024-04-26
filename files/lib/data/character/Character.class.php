<?php

namespace rp\data\character;

use wcf\data\DatabaseObject;
use wcf\system\request\IRouteController;

/**
 * Represents a character.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Free License <https://daries.dev/en/license-for-free-plugins>
 * 
 * @property-read   int $characterID        unique id of the game
 * @property-read   string  $characterName      name of the character
 * @property-read   int|null    $userID     id of the user who created the character, or `null` if not already assigned.
 * @property-read   int $gameID     id of the game for created the character
 * @property-read   int $created        timestamp at which the character has been created
 * @property-read   int $lastUpdateTime     timestamp at which the character has been updated the last time
 * @property-read   string  $notes      notes of the character
 * @property-read   array   $additionalData       array with additional data of the character
 * @property-read   string  $guildName       guild name if character does not belong to this guild
 * @property-read   int $views      number of times the character's profile has been visited
 * @property-read   int $isPrimary      is `1` if the character is primary character of this game, otherwise `0`
 * @property-read   int $isDisabled     is `1` if the character is disabled and thus is not displayed, otherwise `0`
 */
final class Character extends DatabaseObject implements IRouteController
{
    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->characterName;
    }
}
