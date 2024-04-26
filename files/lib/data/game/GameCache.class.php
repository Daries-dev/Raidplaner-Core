<?php

namespace rp\data\game;

use rp\system\cache\builder\GameCacheBuilder;
use wcf\system\SingletonFactory;

/**
 * Manages the game cache.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Free License <https://daries.dev/en/license-for-free-plugins>
 */
final class GameCache extends SingletonFactory
{
    /**
     * cached game ids with game identifier as key
     * @var int[]
     */
    protected array $cachedIdentifier = [];

    /**
     * cached games
     * @var Game[]
     */
    protected array $cachedGames = [];

    /**
     * Returns the current selected game.
     */
    public function getCurrentGame(): Game
    {
        $game = $this->getGameByID(RP_CURRENT_GAME_ID);

        if ($game === null) {
            // fallback to default game
            $game = new Game(null, [
                'identifier' => 'dev.daries.rp.game.default',
            ]);
        }

        return $game;
    }

    /**
     * Returns the game with the given game id or `null` if no such game exists.
     */
    public function getGameByID(int $gameID): ?Game
    {
        return $this->cachedGames[$gameID] ?? null;
    }

    /**
     * Returns the game with the given game identifier or `null` if no such game exists.
     */
    public function getGameByIdentifier(string $identifier): ?Game
    {
        if (!isset($this->cachedIdentifier[$identifier])) return null;
        return $this->getGameByID($this->cachedIdentifier[$identifier]);
    }

    /**
     * Returns all games.
     * 
     * @return  Game[]
     */
    public function getGames(): array
    {
        return $this->cachedGames;
    }

    /**
     * Returns the game with the given game ids.
     * 
     * @return	Game[]
     */
    public function getGamesByID(array $gameIDs): array
    {
        $games = [];

        foreach ($gameIDs as $gameID) {
            $games[] = $this->getGameByID($gameID);
        }

        return $games;
    }

    /**
     * @inheritDoc
     */
    protected function init(): void
    {
        $this->cachedGames = GameCacheBuilder::getInstance()->getData([], 'games');
        $this->cachedIdentifier = GameCacheBuilder::getInstance()->getData([], 'identifier');
    }
}
