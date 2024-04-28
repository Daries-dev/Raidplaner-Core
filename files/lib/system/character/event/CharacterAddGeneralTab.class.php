<?php

namespace rp\system\character\event;

use wcf\system\event\IEvent;
use wcf\system\form\builder\container\TabFormContainer;

/**
 * Add extra fields in general tab for Character add form 
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Free License <https://daries.dev/en/license-for-free-plugins>
 */
final class CharacterAddGeneralTab implements IEvent
{
    public function __construct(
        public readonly TabFormContainer $characterGeneralTab
    ) {
    }
}
