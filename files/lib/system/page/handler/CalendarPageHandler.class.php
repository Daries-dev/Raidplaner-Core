<?php

namespace rp\system\page\handler;

use rp\data\event\ViewableEvent;
use wcf\system\page\handler\AbstractMenuPageHandler;
use wcf\system\WCF;

/**
 * Page handler implementation for the calendar.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class CalendarPageHandler extends AbstractMenuPageHandler
{
    /**
     * @inheritDoc
     */
    public function getOutstandingItemCount($objectID = null): int
    {
        return ViewableEvent::getUnreadEvents();
    }

    /**
     * @inheritDoc
     */
    public function isVisible($objectID = null): bool
    {
        return WCF::getSession()->getPermission('user.rp.canReadEvent');
    }
}
