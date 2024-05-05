<?php

namespace rp\data\event;

use wcf\data\DatabaseObject;
use wcf\data\IUserContent;
use wcf\data\TUserContent;
use wcf\system\request\IRouteController;
use wcf\system\request\LinkHandler;

/**
 * Represents a event.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @property-read   int $eventID        unique id of the event
 * @property-read   int $objectTypeID       id of the event controller object type
 * @property-read   string|null $title      name of the event
 * @property-read   int|null    $userID id of the user who created the event or `null` if the user does not exist anymore
 * @property-read   string  $username       name of the user who created the event
 * @property-read   int $created        timestamp at which the event has been created
 * @property-read   int $startTime      timestamp for start the event
 * @property-read   int $endTime        timestamp for end the event
 * @property-read   int $isFullDay      is `1` if the event occurs all day long, otherwise `0`
 * @property-read   string  $notes      notes of the event
 * @property-read   int $hasEmbeddedObjects     is `1` if there are embedded objects in the event, otherwise `0`
 * @property-read   int $views      number of times the event has been viewed
 * @property-read   int $enableComments     is `1` if comments are enabled for the event, otherwise `0`
 * @property-read   int $comments       number of comments on the event
 * @property-read   int $cumulativeLikes        cumulative result of likes (counting `+1`) and dislikes (counting `-1`) for the event
 * @property-read   array   $additionalData     array with additional data of the event
 * @property-read	int $deleteTime     timestamp at which the event has been deleted
 * @property-read	int $isDeleted      is `1` if the event is in trash bin, otherwise `0`
 * @property-read   int $isCanceled     is `1` if the even is canceled, otherwise `0`
 * @property-read   int $isDisabled     is `1` if the even is disabled, otherwise `0`
 */
final class Event extends DatabaseObject implements IUserContent, IRouteController
{
    use TUserContent;

    /**
     * @inheritDoc
     */
    public function getLink(): string
    {
        return LinkHandler::getInstance()->getLink('Event', [
            'application' => 'rp',
            'object' => $this,
            'forceFrontend' => true
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getTime(): int
    {
        return $this->created;
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->title ?? $this->getController()->getTitle();
    }

    /**
     * @inheritDoc
     */
    protected function handleData($data): void
    {
        parent::handleData($data);

        // unserialize additional data
        $this->data['additionalData'] = (empty($data['additionalData']) ? [] : @\unserialize($data['additionalData']));
    }

    public function __get($name): mixed
    {
        $value = parent::__get($name);

        // treat additional data as data variables if it is an array
        $value ??= $this->data['additionalData'][$name] ?? null;

        return $value;
    }
}
