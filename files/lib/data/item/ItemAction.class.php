<?php

namespace rp\data\item;

use wcf\data\AbstractDatabaseObjectAction;

/**
 * Executes item-related actions.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 *
 * @method  ItemEditor[]    getObjects()
 * @method  ItemEditor  getSingleObject()
 */
class ItemAction extends AbstractDatabaseObjectAction
{
    /**
     * @inheritDoc
     */
    protected $className = ItemEditor::class;

    /**
     * @inheritDoc
     */
    public function create(): Item
    {
        $this->parameters['data']['time'] = TIME_NOW;

        return parent::create();
    }
}
