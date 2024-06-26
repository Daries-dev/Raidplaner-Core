<?php

namespace rp\data\event;

use rp\system\cache\runtime\EventRuntimeCache;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\object\type\ObjectTypeCache;
use wcf\data\user\UserProfile;
use wcf\system\cache\runtime\UserProfileRuntimeCache;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\exception\UserInputException;
use wcf\system\message\embedded\object\MessageEmbeddedObjectManager;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\visitTracker\VisitTracker;
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
     * event object
     */
    protected ?Event $event = null;

    public function appointmentSetStatus(): array
    {
        $additionalData = $this->event->additionalData;
        $appointments = $additionalData['appointments'] ?? ['accepted' => [], 'canceled' => [], 'maybe' => []];

        $appointments = \array_map(
            fn ($users) => \array_filter($users, fn ($userID) => $userID !== WCF::getUser()->userID),
            $appointments
        );

        $appointments[$this->parameters['status']][] = WCF::getUser()->userID;
        $additionalData['appointments'] = $appointments;

        $action = new self([$this->event], 'update', ['data' => [
            'additionalData' => \serialize($additionalData)
        ]]);
        $action->executeAction();

        return [
            'template' => WCF::getTPL()->fetch(
                'userListItem',
                'rp',
                [
                    'user' => new UserProfile(WCF::getUser()),
                ]
            ),
        ];
    }

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
     * Marks events as read.
     */
    public function markAsRead(): void
    {
        $this->parameters['visitTime'] ??= TIME_NOW;

        if (empty($this->objects)) {
            $this->readObjects();
        }

        foreach ($this->getObjects() as $event) {
            VisitTracker::getInstance()->trackObjectVisit(
                'dev.daries.rp.event',
                $event->eventID,
                $this->parameters['visitTime']
            );
        }

        // reset storage
        if (WCF::getUser()->userID) {
            UserStorageHandler::getInstance()->reset([WCF::getUser()->userID], 'rpUnreadEvents');
        }
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

    public function validateAppointmentSetStatus(): void
    {
        $this->readInteger('eventID');
        $this->readString('status');

        $this->event = EventRuntimeCache::getInstance()->getObject($this->parameters['eventID']);
        if ($this->event === null) {
            throw new UserInputException('eventID');
        }

        if ($this->event->objectTypeID !== ObjectTypeCache::getInstance()->getObjectTypeIDByName('dev.daries.rp.event.controller', 'dev.daries.rp.event.controller.appointment')) {
            throw new PermissionDeniedException();
        }

        if (!$this->event->canRead()) {
            throw new PermissionDeniedException();
        }
    }

    /**
     * Validates the mark all as read action.
     */
    public function validateMarkAllAsRead(): void
    {
        // does nothing
    }
}
