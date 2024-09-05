<?php

namespace rp\data\item\database;

use wcf\data\DatabaseObject;

/**
 * Represents a item database.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 *
 * @property-read   string  $databaseName       unique name and textual identifier of the item database
 * @property-read   int|null    $packageID      id of the package the which delivers the item database
 * @property-read   string  $className      name of the PHP class implementing `rp\system\item\database\IItemDatabase` handling search handled data
 */
final class ItemDatabase extends DatabaseObject
{
    /**
     * @inheritDoc
     */
    protected static $databaseTableIndexName = 'databaseName';

    /**
     * @inheritDoc
     */
    protected static $databaseTableIndexIsIdentity = false;

    /**
     * @inheritDoc
     */
    protected static $databaseTableName = 'item_database';
}