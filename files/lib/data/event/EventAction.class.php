<?php

namespace rp\data\event;

use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\message\embedded\object\MessageEmbeddedObjectManager;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\WCF;

/**
 * Executes event-related actions.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @method  EventEditor[]   getObjects()
 * @method  EventEditor     getSingleObject()
 */
class EventAction extends AbstractDatabaseObjectAction
{
    /**
     * @inheritDoc
     */
    public function create(): Event
    {
        $this->parameters['data']['userID'] ??= WCF::getUser()->userID;
        $this->parameters['data']['username'] ??= WCF::getUser()->username;
        $this->parameters['data']['isDisabled'] = WCF::getSession()->getPermission('user.rp.canCreateEventWithoutModeration') ? 0 : 1;
        $this->parameters['data']['created'] = TIME_NOW;
        $this->parameters['data']['gameID'] ??= RP_CURRENT_GAME_ID;

        if (!empty($this->parameters['notes_htmlInputProcessor'])) {
            $this->parameters['data']['notes'] = $this->parameters['notes_htmlInputProcessor']->getHtml();
        }

        /** @var Event $event */
        $event = parent::create();
        $eventEditor = new EventEditor($event);

        // save embedded objects
        if (!empty($this->parameters['notes_htmlInputProcessor'])) {
            /** @noinspection PhpUndefinedMethodInspection */
            $this->parameters['notes_htmlInputProcessor']->setObjectID($event->eventID);
            if (MessageEmbeddedObjectManager::getInstance()->registerObjects($this->parameters['notes_htmlInputProcessor'])) {
                $eventEditor->update(['hasEmbeddedObjects' => 1]);
            }
        }

        if (!$event->isDisabled) {
            $action = new EventAction([$eventEditor], 'triggerPublication');
            $action->executeAction();
        }

        return new Event($event->eventID);
    }

    /**
     * Triggers the publication of events.
     */
    public function triggerPublication(): void
    {
        if (empty($this->objects)) {
            $this->readObjects();
        }

        // reset storage
        UserStorageHandler::getInstance()->resetAll('rpUnreadEvents');
    }

    /**
     * @inheritDoc
     */
    public function update(): void
    {
        if (!empty($this->parameters['notes_htmlInputProcessor'])) {
            $this->parameters['data']['notes'] = $this->parameters['notes_htmlInputProcessor']->getHtml();
        }

        parent::update();

        foreach ($this->getObjects() as $event) {
            // save embedded objects
            if (!empty($this->parameters['notes_htmlInputProcessor'])) {
                /** @noinspection PhpUndefinedMethodInspection */
                $this->parameters['notes_htmlInputProcessor']->setObjectID($event->eventID);
                if ($event->hasEmbeddedObjects != MessageEmbeddedObjectManager::getInstance()->registerObjects($this->parameters['notes_htmlInputProcessor'])) {
                    $event->update(['hasEmbeddedObjects' => $event->hasEmbeddedObjects ? 0 : 1]);
                }
            }
        }
    }
}
