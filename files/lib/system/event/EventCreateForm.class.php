<?php

namespace rp\system\event;

use wcf\system\event\IEvent;
use wcf\system\form\builder\IFormDocument;

/**
 * Indicates that a form is created in the Event Controller.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class EventCreateForm implements IEvent
{
    public function __construct(
        public readonly IFormDocument $form,
        public readonly string $objectController
    ) {
    }
}
