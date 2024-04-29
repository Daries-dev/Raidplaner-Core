<?php

namespace rp\system\character\event;

use wcf\system\event\IEvent;
use wcf\system\form\builder\IFormDocument;

/**
 * Add extra fields in character add form 
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class CharacterAddCreateForm implements IEvent
{
    public function __construct(
        public readonly IFormDocument $form
    ) {
    }
}
