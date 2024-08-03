<?php

use wcf\system\event\EventHandler;

/**
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */

return static function (): void {
    $eventHandler = EventHandler::getInstance();

    $eventHandler->register(
        \rp\event\character\profile\menu\CharacterProfileMenuCollecting::class,
        static function (\rp\event\character\profile\menu\CharacterProfileMenuCollecting $event) {
            $event->register(\rp\system\character\profile\menu\AboutCharacterProfileMenu::class, -100);
        }
    );

    $eventHandler->register(
        \wcf\event\endpoint\ControllerCollecting::class,
        static function (\wcf\event\endpoint\ControllerCollecting $event) {
            $event->register(new \rp\system\endpoint\controller\rp\attendees\CreateAttendee);
            $event->register(new \rp\system\endpoint\controller\rp\attendees\DeleteAttendee);
            $event->register(new \rp\system\endpoint\controller\rp\attendees\RenderAttendee);
            $event->register(new \rp\system\endpoint\controller\rp\attendees\UpdateAttendeeStatus);
            $event->register(new \rp\system\endpoint\controller\rp\events\AvailableCharacters);
        }
    );
};
