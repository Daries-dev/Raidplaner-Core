<?php

namespace rp\data\character;

use wcf\data\DatabaseObjectDecorator;
use wcf\data\ITitledLinkObject;

/**
 * Decorates the character object and provides functions to retrieve data for character profiles.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Free License <https://daries.dev/en/license-for-free-plugins>
 */
final class CharacterProfile extends DatabaseObjectDecorator implements ITitledLinkObject
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = Character::class;

    /**
     * Returns the character profile with the given character name.
     */
    public static function getCharacterProfileByCharacterName(string $name): CharacterProfile
    {
        $character = Character::getCharacterByCharacterName($name);
        return new self($character);
    }

        /**
     * @inheritDoc
     */
    public function getLink(): string
    {
        return $this->getDecoratedObject()->getLink();
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->getDecoratedObject()->getTitle();
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->getDecoratedObject()->__toString();
    }
}
