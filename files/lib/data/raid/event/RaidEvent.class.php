<?php

namespace rp\data\raid\event;

use rp\data\point\account\PointAccount;
use rp\data\point\account\PointAccountCache;
use wcf\data\DatabaseObject;
use wcf\data\ITitledObject;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Represents a raid event.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @property-read   int $eventID        unique id of the raid event
 * @property-read   string  $title      title of the race or name of language item which contains the title
 * @property-read   int|null    $pointAccountID     id of the point account, or `null` if not assigned
 * @property-read   int $gameID     id of the game
 * @property-read   float   $defaultPoints      default points of the raid event
 * @property-read   string  $icon       icon of the raid event
 * @property-read   int $showProfile        is `1` if the raid event is show in profile, otherwise `0`
 */
final class RaidEvent extends DatabaseObject implements ITitledObject
{
    /**
     * point account object
     */
    protected ?PointAccount $pointAccount = null;

    /**
     * Returns the html code to display the icon.
     */
    public function getIcon(?int $size): string
    {
        if ($size === null) $size = $this->size;

        return '<img src="' . StringUtil::encodeHTML($this->getIconPath()) . '" style="width: ' . $size . 'px; height: ' . $size . 'px" alt="" class="raidEventIcon">';
    }

    /**
     * Returns full path to icon.
     */
    public function getIconPath(): string
    {
        if (empty($this->icon)) $this->icon = 'unknown';
        return WCF::getPath('rp') . 'images/raid/event/' . $this->icon . '.png';
    }

    /**
     * Returns the point account with the given point account id or 
     * `null` if no such point account exists.
     */
    public function getPointAccount(): ?PointAccount
    {
        if ($this->pointAccount === null) {
            $this->pointAccount = PointAccountCache::getInstance()->getAccountByID($this->pointAccountID);
        }

        return $this->pointAccount;
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return WCF::getLanguage()->get($this->title);
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->getTitle();
    }
}
