<?php

namespace rp\data\race;

use rp\system\cache\builder\RaceCacheBuilder;
use wcf\system\SingletonFactory;

/**
 * Manages the race cache.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Free License <https://daries.dev/en/license-for-free-plugins>
 */
final class RaceCache extends SingletonFactory
{
    /**
     * cached race ids with race identifier as key
     * @var int[]
     */
    protected array $cachedIdentifier = [];

    /**
     * cached races
     * @var Race[]
     */
    protected array $cachedRaces = [];

    /**
     * Returns the race with the given race id or `null` if no such race exists.
     */
    public function getRaceByID(int $raceID): ?Race
    {
        return $this->cachedRaces[$raceID] ?? null;
    }

    /**
     * Returns the race with the given race identifier or `null` if no such race exists.
     */
    public function getRaceByIdentifier(string $identifier): ?Race
    {
        return $this->getRaceByID($this->cachedIdentifier[$identifier] ?? 0);
    }

    /**
     * Returns all races.
     * 
     * @return	Race[]
     */
    public function getRaces(): array
    {
        return $this->cachedRaces;
    }

    /**
     * Returns the races with the given race ids.
     * 
     * @return	Race[]
     */
    public function getRacesByIDs(array $raceIDs): array
    {
        $returnValues = [];

        foreach ($raceIDs as $raceID) {
            $returnValues[] = $this->getRaceByID($raceID);
        }

        return $returnValues;
    }

    /**
     * @inheritDoc
     */
    protected function init(): void
    {
        $this->cachedIdentifier = RaceCacheBuilder::getInstance()->getData(['gameID' => RP_CURRENT_GAME_ID], 'identifier');
        $this->cachedRaces = RaceCacheBuilder::getInstance()->getData(['gameID' => RP_CURRENT_GAME_ID], 'race');
    }
}
