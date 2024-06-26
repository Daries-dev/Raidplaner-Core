<?php

namespace rp\data\faction;

use rp\data\game\Game;
use rp\data\game\GameCache;
use wcf\data\DatabaseObject;
use wcf\data\ITitledObject;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Represents a faction.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @property-read   int $factionID      unique id of the faction
 * @property-read   int $gameID     id of the game
 * @property-read   string  $identifier     unique textual identifier of the faction identifier
 * @property-read   string  $title      title of the faction or name of language item which contains the title
 * @property-read   string  $icon       icon of the faction
 * @property-read   int $isDisabled     is `1` if the faction is disabled and thus not selectable, otherwise `0`
 * @property-read   int $packageID      id of the package which delivers the faction
 */
final class Faction extends DatabaseObject implements ITitledObject
{
    /**
     * Returns game object.
     */
    public function getGame(): ?Game
    {
        return GameCache::getInstance()->getGameByID($this->gameID);
    }

    /**
     * Returns the html code to display the icon.
     */
    public function getIcon(int $size): string
    {
        if (empty($this->icon)) return '';
        return '<img src="' . StringUtil::encodeHTML($this->getIconPath()) . '" style="width: ' . $size . 'px; height: ' . $size . 'px" alt="" class="gameIcon jsTooltip" title="' . $this->getTitle() . '" loading="lazy">';
    }

    /**
     * Returns full path to icon.
     */
    public function getIconPath(): string
    {
        return WCF::getPath('rp') . 'images/' . $this->getGame()->identifier . '/' . $this->icon . '.png';
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
