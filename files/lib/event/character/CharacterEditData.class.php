<?php

namespace rp\event\character;

use wcf\data\IStorableObject;
use wcf\event\IPsr14Event;
use wcf\system\form\builder\IFormDocument;

/**
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class CharacterEditData implements IPsr14Event
{
    public function __construct(
        public readonly IFormDocument $form,
        public readonly IStorableObject $formObject,
    ) {
    }
}
