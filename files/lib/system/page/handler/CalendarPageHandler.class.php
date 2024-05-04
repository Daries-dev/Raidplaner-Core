<?php

namespace rp\system\page\handler;

use wcf\system\page\handler\AbstractMenuPageHandler;

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
        return 0; // TODO
    }

    /**
     * @inheritDoc
     */
    public function isVisible($objectID = null): bool
    {
        return true; // TODO
    }
}
