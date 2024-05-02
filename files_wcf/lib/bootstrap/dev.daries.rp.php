<?php

use rp\system\character\profile\menu\AboutCharacterProfileMenu;
use rp\system\character\profile\menu\event\CharacterProfileMenuCollecting;
use wcf\system\event\EventHandler;

/**
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */

return static function (): void {
    $eventHandler = EventHandler::getInstance();

    $eventHandler->register(CharacterProfileMenuCollecting::class, static function (CharacterProfileMenuCollecting $event) {
        $event->register(AboutCharacterProfileMenu::class, -100);
    });
};
