<?php

namespace rp\data\role;

use wcf\data\AbstractDatabaseObjectAction;

/**
 * Executes role related actions.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @method  RoleEditor      create()
 * @method  RoleEditor[]    getObjects()
 * @method  RoleEditor      getSingleObject()
 */
class RoleAction extends AbstractDatabaseObjectAction
{
    /**
     * @inheritDoc
     */
    protected $className = RoleEditor::class;

    /**
     * @inheritDoc
     */
    protected $permissionsCreate = ['admin.rp.canManageGame'];

    /**
     * @inheritDoc
     */
    protected $permissionsDelete = ['admin.rp.canManageGame'];

    /**
     * @inheritDoc
     */
    protected $permissionsUpdate = ['admin.rp.canManageGame'];

    /**
     * @inheritDoc
     */
    protected $requireACP = ['create', 'delete', 'update'];
}
