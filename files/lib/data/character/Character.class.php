<?php

namespace rp\data\character;

use rp\system\cache\runtime\CharacterProfileRuntimeCache;
use wcf\data\DatabaseObject;
use wcf\system\request\IRouteController;
use wcf\system\request\LinkHandler;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\WCF;

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
    protected static $databaseTableName = 'member';

    /**
     * Returns the character with the given character name.
     */
    public static function getCharacterByCharacterName(string $name): Character
    {
        $sql = "SELECT  *
                FROM    rp1_member
                WHERE   characterName = ?
                    AND gameID = ?";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([
            $name,
            RP_CURRENT_GAME_ID,
        ]);
        $row = $statement->fetchArray();
        if (!$row) $row = [];

        return new self(null, $row);
    }

    /**
     * @inheritDoc
     */
    public function getLink(): string
    {
        return LinkHandler::getInstance()->getLink('Character', [
            'application' => 'rp',
            'forceFrontend' => true,
            'object' => $this
        ]);
    }

    public function getPrimaryCharacter(): ?CharacterProfile
    {
        if ($this->isPrimary || !$this->userID) {
            return new CharacterProfile($this);
        } else {
            $characterPrimaryIDs = UserStorageHandler::getInstance()->getField('characterPrimaryIDs', $this->userID);

            // cache does not exist or is outdated
            if ($characterPrimaryIDs === null) {
                $sql = "SELECT  gameID, characterID
                        FROM    rp" . WCF_N . "_member
                        WHERE   userID = ?
                            AND isPrimary = ?";
                $statement = WCF::getDB()->prepareStatement($sql);
                $statement->execute([$this->userID, 1]);
                $characterPrimaryIDs = $statement->fetchMap('gameID', 'characterID');

                // update storage characterPrimaryIDs
                UserStorageHandler::getInstance()->update(
                    $this->userID,
                    'characterPrimaryIDs',
                    \serialize($characterPrimaryIDs)
                );
            } else {
                $characterPrimaryIDs = \unserialize($characterPrimaryIDs);
            }

            return CharacterProfileRuntimeCache::getInstance()->getObject($characterPrimaryIDs[$this->gameID]);
        }
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->characterName;
    }

    protected function handleData($data): void
    {
        parent::handleData($data);

        // unserialize additional data
        $this->data['additionalData'] = (empty($data['additionalData']) ? [] : @\unserialize($data['additionalData']));
    }

    public function __get($name): mixed
    {
        $value = parent::__get($name);

        // treat additional data as data variables if it is an array
        $value ??= $this->data['additionalData'][$name] ?? null;

        return $value;
    }
}
