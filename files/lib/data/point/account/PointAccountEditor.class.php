<?php

namespace rp\data\point\account;

use rp\system\cache\builder\PointAccountCacheBuilder;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;

/**
 * Provides functions to edit point account.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @method static   PointAccount    create(array $parameters = [])
 * @method  PointAccount  
 */
class PointAccountEditor extends DatabaseObjectEditor implements IEditableCachedObject
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = PointAccount::class;

    /**
     * @inheritDoc
     */
    public static function resetCache(): void
    {
        PointAccountCacheBuilder::getInstance()->reset(); //TODO
        //CharacterPointCacheBuilder::getInstance()->reset(); //TODO
    }
}
