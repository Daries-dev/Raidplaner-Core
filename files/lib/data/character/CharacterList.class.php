<?php

namespace rp\data\character;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of characters.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 *
 * @method  Character   current()
 * @method  Character[] getObjects()
 * @method  Character|null  search($objectID)
 * @property    Character[] $objects
 */
class CharacterList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = Character::class;
}
