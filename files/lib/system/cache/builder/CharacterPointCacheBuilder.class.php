<?php

namespace rp\system\cache\builder;

use rp\data\character\CharacterProfile;
use rp\data\character\CharacterProfileList;
use rp\data\point\account\PointAccount;
use rp\data\point\account\PointAccountCache;
use wcf\system\cache\builder\AbstractCacheBuilder;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\WCF;

/**
 * Cached the points of the primary character and its twinks.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class CharacterPointCacheBuilder extends AbstractCacheBuilder
{
    /**
     * Provides the default data structure for point information.
     */
    protected function getDefaultData(): array
    {
        return [
            'received' => [
                'color' => '',
                'points' => 0
            ],
            'issued' => [
                'color' => '',
                'points' => 0
            ],
            'adjustments' => [
                'color' => '',
                'points' => 0
            ],
            'current' => [
                'color' => '',
                'points' => 0
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    protected function rebuild(array $parameters): array
    {
        $data = [];

        $pointAccounts = PointAccountCache::getInstance()->getAccounts();

        $characterList = new CharacterProfileList();
        $characterList->getConditionBuilder()->add('userID = ?', [$parameters['userID']]);
        $characterList->readObjects();
        $characters = $characterList->getObjects();
        $characterIDs = \array_keys($characters);

        $characterItems = []; // TODO items

        $conditionBuilder = new PreparedStatementConditionBuilder();
        $conditionBuilder->add('attendee.characterID IN (?)', [$characterIDs]);
        $sql = "SELECT      SUM(raid.points) as points, raid_event.pointAccountID, attendee.characterID
                FROM        rp1_raid_attendee attendee
                LEFT JOIN   rp1_raid raid
                    ON      (raid.raidID = attendee.raidID)
                LEFT JOIN   rp1_raid_event raid_event
                    ON      (raid.raidEventID = raid_event.eventID)
                            " . $conditionBuilder . "
                GROUP BY    attendee.characterID, raid_event.pointAccountID";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute($conditionBuilder->getParameters());

        $characterPoints = [];
        while ($row = $statement->fetchArray()) {
            $characterPoints[$row['characterID']] ??= [];
            $characterPoints[$row['characterID']][$row['pointAccountID']] = $row['points'];
        }

        /** @var CharacterProfile $character */
        foreach ($characters as $character) {
            $characterID = $character->getObjectID();
            $data[$characterID] = [];

            /** @var PointAccount $pointAccount */
            foreach ($pointAccounts as $pointAccount) {
                $pointAccountID = $pointAccount->getObjectID();
                $data[$characterID][$pointAccountID] = $this->getDefaultData();

                $receivedPoints = $characterPoints[$characterID][$pointAccountID] ?? 0;
                $data[$characterID][$pointAccountID]['received']['points'] = $receivedPoints;
                $data[$characterID][$pointAccountID]['received']['color'] = $receivedPoints > 0 ? 'green' : '';

                $issuedPoints = $characterItems[$characterID][$pointAccountID] ?? 0;
                $data[$characterID][$pointAccountID]['issued']['points'] = $issuedPoints;
                $data[$characterID][$pointAccountID]['issued']['color'] = $issuedPoints > 0 ? 'red' : '';

                $currentPoints = $receivedPoints - $issuedPoints;
                $data[$characterID][$pointAccountID]['current']['points'] = $currentPoints;
                $data[$characterID][$pointAccountID]['current']['color'] = $currentPoints < 0 ? 'red' : ($currentPoints > 0 ? 'green' : '');
            }
        }
        return $data;
    }
}
