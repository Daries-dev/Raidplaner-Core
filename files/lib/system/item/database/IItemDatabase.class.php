<?php

namespace rp\system\item\database;

use wcf\data\language\Language;

/**
 * Default interface for item database.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
interface IItemDatabase
{
    /**
     * Return item array, with all information about an item.
     * 
     * The returned array must at least contain the following keys:
     * - 'content' (string)
     * - 'icon' (string)
     * - 'iconExtension' (string)
     * - 'iconURL' (string)
     * - 'name' (string)
     * 
     * Example structure:
     * $itemData = [
     *     'content' => '',
     *     'icon' => '',
     *     'iconExtension' => '',
     *     'iconURL' => '',
     *     'name' => '',
     * ];
     * 
     * Additional fields may be included in the array, but the above keys are required.
     */
    public function getItemData(string|int $itemID, ?Language $language = null): ?array;

    /**
     * Searches an item id for an item name.
     */
    public function searchItemID(string $itemName, ?Language $language = null): int|string;
}
