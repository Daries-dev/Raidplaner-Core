<?php

namespace rp\system\event;

use wcf\system\form\builder\IFormDocument;

/**
 * Interface for dynamic event controller.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
interface IEventController
{
    /**
     * Creates the form object.
     * 
     * This is the method that is intended to be overwritten by child classes
     * to add the form containers and fields.
     */
    public function createForm(IFormDocument $form): void;

    /**
     * Returns true if the current user can use this event provider.
     */
    public function isAccessible(): bool;

    /**
     * Returns the data of the form which should be saved.
     */
    public function saveForm(array $formData): array;
}
