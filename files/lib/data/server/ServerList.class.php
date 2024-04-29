<?php

namespace rp\data\server;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of servers.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Free License <https://daries.dev/en/license-for-free-plugins>
 *
 * @method  Server      current()
 * @method  Server[]    getObjects()
 * @method  Server|null     search($objectID)
 * @property    Server[]    $objects
 */
class ServerList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = Server::class;
}
