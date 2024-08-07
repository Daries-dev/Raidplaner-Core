<?php

namespace rp\system\event;

use rp\data\event\Event;
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
     * Checks the permissions of this event controller.
     */
    public function checkPermissions(): void;

    /**
     * Creates the form object.
     * 
     * This is the method that is intended to be overwritten by child classes
     * to add the form containers and fields.
     */
    public function createForm(IFormDocument $form): void;

    /**
     * Returns the template in the main section of the event.
     */
    public function getContent(): string;

    /**
     * Returns the header navigation.
     */
    public function getContentHeaderNavigation(): string;

    /**
     * Returns the database object of this event.
     */
    public function getEvent(): ?Event;

    /**
     * Returns the html code to display the icon.
     */
    public function getIcon(int $size = 16): string;

    /**
     * Returns the title of the event.
     */
    public function getTitle(): string;

    /**
     * Returns true if the current user can use this event provider.
     */
    public function isAccessible(): bool;

    /**
     * Checks whether time for this event is expired
     */
    public function isExpired(): bool;

    /**
     * Returns the data of the form which should be saved.
     */
    public function saveForm(array $formData): array;

    /**
     * Sets the database object of this event.
     */
    public function setEvent(Event $event): void;

    /**
     * Returns `true` if the position matches the event nodes position present in 
     * the controller, otherwise `false`.
     */
    public function showEventNodes(string $position): bool;
}
