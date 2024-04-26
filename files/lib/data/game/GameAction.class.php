<?php

namespace rp\data\game;

use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\WCF;

/**
 * Game related actions.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Free License <https://daries.dev/en/license-for-free-plugins>
 * 
 * @method  GameEditor[]    getObjects()
 * @method  GameEditor      getSingleObject()
 */
class GameAction extends AbstractDatabaseObjectAction
{
    /**
     * @inheritDoc
     */
    protected $className = GameEditor::class;

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
