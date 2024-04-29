<?php

namespace rp\data\classification;

use wcf\data\AbstractDatabaseObjectAction;

/**
 * Executes classification related actions.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Free License <https://daries.dev/en/license-for-free-plugins>
 * 
 * @method  ClassificationEditor[]      getObjects()
 * @method  ClassificationEditor        getSingleObject()
 */
class ClassificationAction extends AbstractDatabaseObjectAction
{
    /**
     * @inheritDoc
     */
    protected $className = ClassificationEditor::class;

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
