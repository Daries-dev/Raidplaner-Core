<?php

namespace rp\system\page\handler;

use rp\data\event\ViewableEventList;
use rp\system\cache\runtime\ViewableEventRuntimeCache;
use wcf\data\page\Page;
use wcf\data\user\online\UserOnline;
use wcf\system\page\handler\AbstractLookupPageHandler;
use wcf\system\page\handler\IOnlineLocationPageHandler;
use wcf\system\page\handler\TOnlineLocationPageHandler;
use wcf\system\WCF;

/**
 * Page handler implementation for the event.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class EventPageHandler extends AbstractLookupPageHandler implements IOnlineLocationPageHandler
{
    use TOnlineLocationPageHandler;

    /**
     * @inheritDoc
     */
    public function getLink($objectID): string
    {
        return ViewableEventRuntimeCache::getInstance()->getObject($objectID)->getLink();
    }

    /**
     * @inheritDoc
     */
    public function getOnlineLocation(Page $page, UserOnline $user): string
    {
        if ($user->pageObjectID === null) {
            return '';
        }

        $event = ViewableEventRuntimeCache::getInstance()->getObject($user->pageObjectID);
        if ($event === null || !$event->canRead()) {
            return '';
        }

        return WCF::getLanguage()->getDynamicVariable('wcf.page.onlineLocation.' . $page->identifier, ['event' => $event]);
    }

    /**
     * @inheritDoc
     */
    public function isValid($objectID): bool
    {
        return ViewableEventRuntimeCache::getInstance()->getObject($objectID) !== null;
    }

    /**
     * @inheritDoc
     */
    public function isVisible($objectID = null): bool
    {
        return ViewableEventRuntimeCache::getInstance()->getObject($objectID)->canRead();
    }

    /**
     * @inheritDoc
     */
    public function lookup($searchString): array
    {
        $eventList = new ViewableEventList();
        $eventList->getConditionBuilder()->add('event.title LIKE ?', ['%' . $searchString . '%']);
        $eventList->sqlLimit = 10;
        $eventList->sqlOrderBy = 'event.title';
        $eventList->readObjects();

        $results = [];
        foreach ($eventList->getObjects() as $event) {
            $results[] = [
                'description' => $event->getExcerpt(),
                'image' => $event->getUserProfile()->getAvatar()->getImageTag(48),
                'link' => $event->getLink(),
                'objectID' => $event->eventID,
                'title' => $event->getTitle()
            ];
        }

        return $results;
    }

    /**
     * @inheritDoc
     */
    public function prepareOnlineLocation(Page $page, UserOnline $user): void
    {
        if ($user->pageObjectID !== null) {
            ViewableEventRuntimeCache::getInstance()->cacheObjectID($user->pageObjectID);
        }
    }
}
