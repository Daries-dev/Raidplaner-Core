<?php

namespace rp\data\role;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of roles.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Free License <https://daries.dev/en/license-for-free-plugins>
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
