<?php

namespace rp\form;

/**
 * Shows the character add form.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
class CharacterEditForm extends \rp\acp\form\CharacterEditForm
{
    /**
     * @inheritDoc
     */
    public $neededPermissions = [];

    /**
     * @inheritDoc
     */
    public function readParameters(): void
    {
        parent::readParameters();

        if (!$this->formObject->canEdit()) {
            throw new PermissionDeniedException();
        }
    }
}