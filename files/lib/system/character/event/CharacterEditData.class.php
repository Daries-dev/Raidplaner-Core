<?php

namespace rp\system\character\event;

use wcf\system\event\IEvent;
use wcf\system\form\builder\IFormDocument;

/**
 * Add extra fields in general tab for Character add form 
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Free License <https://daries.dev/en/license-for-free-plugins>
 */
final class CharacterEditData implements IEvent
{
    public function __construct(
        public readonly IFormDocument $form
    ) {
    }
}
