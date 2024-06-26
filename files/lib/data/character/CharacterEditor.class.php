<?php

namespace rp\data\character;

use wcf\data\DatabaseObjectEditor;

/**
 * Provides functions to edit characters.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @method static   Character   create(array $parameters = [])
 * @method  Character   getDecoratedObject()
 * @mixin   Character
 */
class CharacterEditor extends DatabaseObjectEditor
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = Character::class;
}
