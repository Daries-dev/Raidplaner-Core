<?php

namespace rp\data\event;

use wcf\data\DatabaseObjectEditor;

/**
 * Provides functions to edit events.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @method static   Event   create(array $parameters = [])
 * @method  Event   getDecoratedObject()
 * @mixin   Event
 */
class EventEditor extends DatabaseObjectEditor
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = Event::class;
}
