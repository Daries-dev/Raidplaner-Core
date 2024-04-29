<?php

namespace rp\data\role;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of roles.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 *
 * @method  Role        current()
 * @method  Role[]      getObjects()
 * @method  Role|null   search($objectID)
 * @property    Role[]  $objects
 */
class RoleList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = Role::class;
}
