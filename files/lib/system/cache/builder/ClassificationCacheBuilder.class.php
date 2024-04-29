<?php

namespace rp\system\cache\builder;

use rp\data\classification\Classification;
use wcf\system\cache\builder\AbstractCacheBuilder;
use wcf\system\WCF;

/**
 * Caches the classification.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class ClassificationCacheBuilder extends AbstractCacheBuilder
{
    /**
     * @inheritDoc
     */
    protected function rebuild(array $parameters): array
    {
        $data = [
            'identifier' => [],
            'classification' => [],
        ];

        // get game classification
        $sql = "SELECT  *
                FROM    rp1_classification
                WHERE   isDisabled = ?
                    AND gameID = ?";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([
            0,
            $parameters['gameID'],
        ]);

        /** @var Classification $object */
        while ($object = $statement->fetchObject(Classification::class)) {
            $data['classification'][$object->classificationID] = $object;
            $data['identifier'][$object->identifier] = $object->classificationID;
        }

        return $data;
    }
}
