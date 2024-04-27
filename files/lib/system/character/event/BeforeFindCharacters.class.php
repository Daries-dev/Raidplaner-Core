<?php

namespace rp\system\character\event;

use wcf\system\event\IEvent;

/**
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Free License <https://daries.dev/en/license-for-free-plugins>
 */
final class BeforeFindCharacters implements IEvent
{
    public function __construct(
        private readonly string $searchString
    ) {
    }

    public function getSearchString(): string
    {
        return $this->searchString;
    }

    public function setSearchString(string $searchString): void
    {
        $this->searchString = $searchString;
    }
}
