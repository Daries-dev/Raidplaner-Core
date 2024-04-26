<?php

namespace rp\data\character;

use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\request\RequestHandler;
use wcf\system\user\storage\UserStorageHandler;

/**
 * Executes character related actions.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Free License <https://daries.dev/en/license-for-free-plugins>
 * 
 * @method  CharacterEditor[]    getObjects()
 * @method  CharacterEditor  getSingleObject()
 */
class CharacterAction extends AbstractDatabaseObjectAction
{
    /**
     * @inheritDoc
     */
    protected $className = CharacterEditor::class;

    /**
     * @inheritDoc
     */
    protected $permissionsCreate = ['admin.rp.canAddCharacter'];

    /**
     * @inheritDoc
     */
    protected $permissionsDelete = ['admin.rp.canDeleteCharacter'];

    /**
     * @inheritDoc
     */
    protected $permissionsUpdate = ['admin.rp.canEditCharacter'];

    /**
     * @inheritDoc
     */
    public function create(): Character
    {
        $this->parameters['data']['created'] = TIME_NOW;

        if ($this->parameters['data']['userID'] !== null) {
            if (RequestHandler::getInstance()->isACPRequest()) {
                $characterList = new CharacterList();
                $characterList->getConditionBuilder()->add('userID = ?', [$this->parameters['data']['userID']]);
                $characterList->getConditionBuilder()->add('gameID = ?', [RP_CURRENT_GAME_ID]);
                $characterList->getConditionBuilder()->add('isPrimary = ?', [1]);
                $this->parameters['data']['isPrimary'] = \intval($characterList->countObjects() === 0);
            } else {
                $this->parameters['data']['isPrimary'] = 0; // TODO
            }
        } else {
            $this->parameters['data']['isPrimary'] = 1;
            $this->parameters['data']['isDisabled'] = 1;
        }

        /** @var Character $character */
        $character = parent::create();

        if ($character->userID) {
            UserStorageHandler::getInstance()->reset([$character->userID], 'characterPrimaryIDs');
        }

        return $character;
    }

    /**
     * @inheritDoc
     */
    public function update(): void
    {
        $this->parameters['data']['lastUpdateTime'] = TIME_NOW;

        if ($this->parameters['data']['userID'] === null) {
            $this->parameters['data']['isDisabled'] = 1;
        }

        parent::update();
    }
}
