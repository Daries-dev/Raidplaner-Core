<?php

namespace rp\data\character\avatar;

/**
 * Any displayable avatar type should implement this class.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
interface ICharacterAvatar
{
    /**
     * Returns the height of this avatar.
     */
    public function getHeight(): int;

    /**
     * Returns the html code to display this avatar.
     */
    public function getImageTag(?int $size = null): string;

    /**
     * Returns the url to this avatar.
     */
    public function getURL(?int $size = null): string;

    /**
     * Returns the width of this avatar.
     */
    public function getWidth(): int;
}
