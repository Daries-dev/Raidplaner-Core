<?php

namespace rp\system\character\event;

use wcf\system\event\IEvent;
use wcf\system\form\builder\IFormDocument;

/**
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class CharacterEditData implements IEvent
{
    public function __construct(
        public readonly IFormDocument $form
    ) {
    }
}
