<?php

namespace rp\system\item;

use rp\data\item\database\ItemDatabase;
use rp\data\item\database\ItemDatabaseList;
use rp\data\item\Item;
use rp\data\item\ItemAction;
use rp\data\item\ItemCache;
use SessionHandler;
use wcf\data\user\User;
use wcf\system\language\LanguageFactory;
use wcf\system\SingletonFactory;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Handles items.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class ItemHandler extends SingletonFactory
{
    /**
     * item database list
     */
    protected ?ItemDatabaseList $databases = null;

    /**
     * Returns an item based on the item name
     */
    final public function getSearchItem(string $itemName, int $itemID = 0, bool $refresh = false, array $data = []): Item
    {
        $itemName = StringUtil::trim($itemName);
        if (empty($itemName) && $itemID === 0) {
            return null;
        }

        $item = $searchItemID = null;
        if ($itemID) {
            $item = ItemCache::getInstance()->getItemByID($itemID);
            if ($item) {
                $searchItemID = $item->searchItemID;
            }
        } else {
            $item = ItemCache::getInstance()->getItemByName($itemName);
            if ($item) {
                $searchItemID = $item->searchItemID;
            }
        }

        if ($item === null || $refresh) {
            $newItem = null;
            $user = WCF::getUser();
            try {
                SessionHandler::getInstance()->changeUser(new User(null), true);
                if (!WCF::debugModeIsEnabled()) {
                    \ob_start();
                }

                if ($this->databases !== null) {
                    /** @var ItemDatabase $database */
                    foreach ($this->databases as $database) {
                        $parser = new $database->className();

                        foreach (LanguageFactory::getInstance()->getLanguages() as $language) {
                            $searchData = [];

                            if ($searchItemID === null || empty($searchItemID)) {
                                $searchData = $parser->searchItemID($itemName, $language);
                            } else {
                                $searchData = [
                                    $searchItemID,
                                    $data['type'] ?? 'items'
                                ];
                            }

                            try {
                                $newItem = $parser->getItemData(
                                    $searchData[0],
                                    $language,
                                    $searchData[1]
                                );
                            } catch (\Exception $e) {
                                // Handle exception if necessary
                            }

                            if ($newItem !== null) break;
                        }

                        if ($newItem !== null) break;
                    }
                }
            } catch (\Exception $e) {
                // Handle exception if necessary
            } finally {
                if (!WCF::debugModeIsEnabled()) {
                    \ob_end_clean();
                }
                SessionHandler::getInstance()->changeUser($user, true);
            }

            $saveSearchItemID = '';
            if ($newItem !== null) {
                $saveSearchItemID = $newItem['id'];
                unset($newItem['id']);
            } else {
                $newItem = [];
            }

            if ($item) {
                $action = new ItemAction([$item], 'update', ['data' => [
                    'additionalData' => \serialize($newItem),
                    'searchItemID' => $saveSearchItemID,
                ]]);
                $action->executeAction();

                // reload item
                $item = new Item($item->itemID);
            } else {
                $action = new ItemAction([], 'create', ['data' => [
                    'additionalData' => \serialize($newItem),
                    'itemName' => $itemName,
                    'searchItemID' => $saveSearchItemID,
                ]]);
                $item = $action->executeAction()['returnValues'];
            }
        }

        return $item;
    }

    /**
     * @inheritDoc
     */
    protected function init(): void
    {
        if (!empty(RP_ITEM_DATABASES)) {
            $list = new ItemDatabaseList();
            $list->getConditionBuilder()->add('databaseName IN (?)', [\explode(',', RP_ITEM_DATABASES)]);
            $list->readObjects();
            $this->databases = $list;
        }
    }
}
