<?php

namespace rp\form;

/**
 * Shows the character add form.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
class CharacterAddForm extends \rp\acp\form\CharacterAddForm
{
    /**
     * @inheritDoc
     */
    public $neededPermissions = ['user.rp.canAddCharacter'];

    /**
     * @inheritDoc
     */
    public $objectEditLinkController = CharacterEditForm::class;

}