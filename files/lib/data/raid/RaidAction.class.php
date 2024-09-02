<?php

namespace rp\data\raid;

use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\WCF;

/**
 * Executes raid related actions.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @method  RaidEditor[]    getObjects()
 * @method  RaidEditor  getSingleObject()
 */
class RaidAction extends AbstractDatabaseObjectAction
{
    /**
     * @inheritDoc
     */
    protected $permissionsCreate = ['mod.rp.canAddRaid'];

    /**
     * @inheritDoc
     */
    protected $permissionsDelete = ['mod.rp.canDeleteRaid'];

    /**
     * @inheritDoc
     */
    protected $className = RaidEditor::class;

    /**
     * @inheritDoc
     */
    public function create(): Raid
    {
        $this->parameters['data']['gameID'] = RP_CURRENT_GAME_ID;
        $this->parameters['data']['addedBy'] = WCF::getUser()->username;

        $raid = parent::create();
        $raidEditor = new RaidEditor($raid);

        $attendeeIDs = $this->parameters['attendeeIDs'] ?? [];
        $raidEditor->addAttendees($attendeeIDs, false, $this->parameters['event']);

        return $raid;
    }

    /**
     * @inheritDoc
     */
    public function update(): void
    {
        $this->parameters['data']['updatedBy'] = WCF::getUser()->username;

        parent::update();
    }
}
