<?php

namespace rp\system\character\profile\menu\event;

use rp\system\character\profile\menu\RegisteredCharacterProfileMenu;
use wcf\system\event\IEvent;

/**
 * Requests the collecting of menus that should be included in the list of character menus.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class CharacterProfileMenuCollecting implements IEvent
{
    private \SplPriorityQueue $queue;

    public function __construct()
    {
        $this->queue = new \SplPriorityQueue();
    }

    /**
     * @return iterable<RegisteredCharacterProfileMenu>
     */
    public function getMenus(): iterable
    {
        yield from clone $this->queue;
    }

    /**
     * Registers a new menu.
     */
    public function register(string $className, int $niceValue): void
    {
        $this->queue->insert(new RegisteredCharacterProfileMenu($className), -$niceValue);
    }
}
