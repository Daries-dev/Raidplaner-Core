<?php

namespace rp\data\character;

use rp\system\character\event\BeforeFindCharacters;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\ISearchAction;
use wcf\system\clipboard\ClipboardHandler;
use wcf\system\event\EventHandler;
use wcf\system\exception\UserInputException;
use wcf\system\request\RequestHandler;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\WCF;

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
class CharacterAction extends AbstractDatabaseObjectAction implements ISearchAction
{
    /**
     * @inheritDoc
     */
    protected $allowGuestAccess = ['getSearchResultList'];

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
     * Disables characters.
     */
    public function disable(): void
    {
        foreach ($this->getObjects() as $character) {
            $character->update([
                'isDisabled' => 1
            ]);
        }

        $this->unmarkItems();
    }

    /**
     * Enables characters.
     */
    public function enable(): void
    {
        foreach ($this->getObjects() as $character) {
            $character->update([
                'isDisabled' => 0
            ]);
        }

        $this->unmarkItems();
    }

    /**
     * @inheritDoc
     */
    public function getSearchResultList(): array
    {
        $searchString = $this->parameters['data']['searchString'];
        $excludedSearchValues = $this->parameters['data']['excludedSearchValues'] ?? [];
        $list = [];

        // find characters
        $searchString = \addcslashes($searchString, '_%');

        $event = new BeforeFindCharacters($searchString);
        EventHandler::getInstance()->fire($event);

        $characterProfileList = new CharacterProfileList();
        $characterProfileList->getConditionBuilder()->add("characterName LIKE ?", [$event->getSearchString() . '%']);
        if (!empty($excludedSearchValues)) {
            $characterProfileList->getConditionBuilder()->add("characterName NOT IN (?)", [$excludedSearchValues]);
        }
        $characterProfileList->sqlLimit = 10;
        $characterProfileList->readObjects();

        foreach ($characterProfileList as $characterProfile) {
            $list[] = [
                'icon' => '', // TODO ICON
                'label' => $characterProfile->characterName,
                'objectID' => $characterProfile->characterID,
            ];
        }

        return $list;
    }

    protected function unmarkItems(?array $characterIDs = null): void
    {
        $characterIDs ??= $this->getObjectIDs();

        if (!empty($characterIDs)) {
            ClipboardHandler::getInstance()->unmark(
                $characterIDs,
                ClipboardHandler::getInstance()->getObjectTypeID('dev.daries.rp.character')
            );
        }
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

    /**
     * Validates the disable action.
     */
    public function validateDisable()
    {
        $this->validateEnable();
    }

    /**
     * Validates the enable action.
     */
    public function validateEnable(): void
    {
        WCF::getSession()->checkPermissions(['admin.rp.canEnableCharacter']);

        if (empty($this->objects)) {
            $this->readObjects();
        }
    }

    /**
     * @inheritDoc
     */
    public function validateGetSearchResultList(): void
    {
        $this->readString('searchString', false, 'data');

        if (isset($this->parameters['data']['excludedSearchValues']) && !\is_array($this->parameters['data']['excludedSearchValues'])) {
            throw new UserInputException('excludedSearchValues');
        }
    }
}
