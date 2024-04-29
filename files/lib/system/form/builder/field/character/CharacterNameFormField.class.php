<?php

namespace rp\system\form\builder\field\character;

use wcf\system\form\builder\field\TextFormField;

/**
 * Implementation of a form field for character name.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
class CharacterNameFormField extends TextFormField
{
    /**
     * @inheritDoc
     */
    protected $templateApplication = 'rp';

    /**
     * @inheritDoc
     */
    protected $templateName = 'shared_characterNameFormField';
}
