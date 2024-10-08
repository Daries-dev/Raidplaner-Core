<?php

namespace rp\page;

use CuyZ\Valinor\Mapper\MappingError;
use rp\data\classification\ClassificationCache;
use rp\data\raid\Raid;
use rp\system\cache\runtime\RaidRuntimeCache;
use wcf\http\Helper;
use wcf\page\AbstractPage;
use wcf\system\exception\IllegalLinkException;
use wcf\system\WCF;

/**
 * Shows the raid page.
 *
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class RaidPage extends AbstractPage
{
    /**
     * class distributions
     */
    public array $classDistributions = [];

    /**
     * raid object
     */
    public Raid $raid;

    /**
     * @inheritDoc
     */
    public function assignVariables(): void
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'classDistributions' => $this->classDistributions,
            'raid' => $this->raid
        ]);
    }

    /**
     * @inheritDoc
     */
    public function readData(): void
    {
        parent::readData();

        $attendees = $this->raid->getAttendees();
        $classDistributions = [];
        foreach ($attendees as $attendee) {
            $classificationID = $attendee['classificationID'];
            $classification = ClassificationCache::getInstance()->getClassificationByID($classificationID);

            if ($classification === null) {
                continue;
            }

            if (!isset($classDistributions[$classificationID])) {
                $classDistributions[$classificationID] = [
                    'attendees' => [],
                    'count' => 0,
                    'object' => $classification,
                    'percent' => 0,
                ];
            }

            $classDistributions[$classificationID]['count']++;
            $classDistributions[$classificationID]['attendees'][] = $attendee;
        }

        $totalAttendees = count($attendees);
        foreach ($classDistributions as $classificationID => $distribution) {
            $classDistributions[$classificationID]['percent'] = $totalAttendees > 0
                ? \number_format(($distribution['count'] / $totalAttendees) * 100)
                : 0;
        }

        $this->classDistributions = $classDistributions;
    }

    /**
     * @inheritDoc
     */
    public function readParameters(): void
    {
        parent::readParameters();

        try {
            $parameters = Helper::mapQueryParameters(
                $_GET,
                <<<'EOT'
                    array {
                        id: positive-int
                    }
                    EOT
            );

            $this->raid = RaidRuntimeCache::getInstance()->getObject($parameters['id']);
            if ($this->raid === null) {
                throw new IllegalLinkException();
            }
        } catch (MappingError) {
            throw new IllegalLinkException();
        }

        $this->canonicalURL = $this->raid->getLink();
    }
}
