<?php

namespace rp\data\character;

/**
 * Represents a list of character profiles.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Free License <https://daries.dev/en/license-for-free-plugins>
 *
 * @method  CharacterProfile    current()
 * @method  CharacterProfile[]  getObjects()
 * @method  CharacterProfile|null   search($objectID)
 * @property    CharacterProfile[]  $objects
 */
class CharacterProfileList extends CharacterList
{
    /**
     * @inheritDoc
     */
    public $sqlOrderBy = 'characterName';

    /**
     * @inheritDoc
     */
    public $decoratorClassName = CharacterProfile::class;

    /**
     * @inheritDoc
     */
    public function readObjects(): void
    {
        if ($this->objectIDs === null) {
            $this->readObjectIDs();
        }

        parent::readObjects();
    }
}
