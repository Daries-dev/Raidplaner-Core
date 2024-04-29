<?php

namespace rp\data\classification;

use rp\system\cache\builder\ClassificationCacheBuilder;
use wcf\system\SingletonFactory;

/**
 * Manages the classification cache.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Free License <https://daries.dev/en/license-for-free-plugins>
 */
final class ClassificationCache extends SingletonFactory
{
    /**
     * cached classifications
     * @var Classification[]
     */
    protected array $cachedClassifications = [];

    /**
     * cached classification ids with classification identifier as key
     * @var int[]
     */
    protected array $cachedIdentifier = [];

    /**
     * Returns the classification with the given classification id or `null` if no such classification exists.
     */
    public function getClassificationByID(int $classificationID): ?Classification
    {
        return $this->cachedClassifications[$classificationID] ?? null;
    }

    /**
     * Returns the classification with the given classification identifier or `null` if no such classification exists.
     */
    public function getClassificationByIdentifier(string $identifier): ?Classification
    {
        return $this->getClassificationByID($this->cachedIdentifier[$identifier] ?? 0);
    }

    /**
     * Returns all classifications.
     * 
     * @return	Classification[]
     */
    public function getClassifications(): array
    {
        return $this->cachedClassifications;
    }

    /**
     * Returns the classifications with the given classification ids.
     * 
     * @return	Classification[]
     */
    public function getClassificationsByIDs(array $classificationIDs): array
    {
        $returnValues = [];

        foreach ($classificationIDs as $classificationID) {
            $returnValues[] = $this->getClassificationByID($classificationID);
        }

        return $returnValues;
    }

    /**
     * @inheritDoc
     */
    protected function init(): void
    {
        $this->cachedClassifications = ClassificationCacheBuilder::getInstance()->getData(['gameID' => RP_CURRENT_GAME_ID], 'classification');
        $this->cachedIdentifier = ClassificationCacheBuilder::getInstance()->getData(['gameID' => RP_CURRENT_GAME_ID], 'identifier');
    }
}
