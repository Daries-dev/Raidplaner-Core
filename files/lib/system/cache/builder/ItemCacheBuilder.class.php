<?php

namespace rp\system\cache\builder;

use rp\data\item\ItemList;
use wcf\system\cache\builder\AbstractCacheBuilder;

/**
 * Caches all items.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class ItemCacheBuilder extends AbstractCacheBuilder
{
    /**
     * @inheritDoc
     */
    public function rebuild(array $parameters): array
    {
        $data = [
            'itemNames' => [],
            'items' => [],
        ];

        $itemList = new ItemList();
        $itemList->readObjects();
        foreach ($itemList as $item) {
            $data['items'][$item->itemID] = $item;
            $data['itemNames'][\base64_encode($item->itemName)] = $item->itemID;
        }

        return $data;
    }
}
