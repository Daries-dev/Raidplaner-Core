<?php

namespace rp\data\item\database;

use wcf\data\AbstractDatabaseObjectAction;

/**
 * Executes item database-related actions.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 *
 * @method  ItemDatabase    create()
 * @method  ItemDatabaseEditor[]    getObjects()
 * @method  ItemDatabaseEditor  getSingleObject()
 */
class ItemDatabaseAction extends AbstractDatabaseObjectAction
{
    /**
     * @inheritDoc
     */
    protected $className = ItemDatabaseEditor::class;
}
