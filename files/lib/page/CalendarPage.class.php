<?php

namespace rp\page;

use CuyZ\Valinor\Mapper\MappingError;
use rp\system\calendar\Calendar;
use wcf\data\object\type\ObjectTypeCache;
use wcf\http\Helper;
use wcf\page\AbstractPage;
use wcf\system\exception\IllegalLinkException;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\DateUtil;
use wcf\util\HeaderUtil;

/**
 * Shows the calendar page.
 *
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
final class CalendarPage extends AbstractPage
{
    /**
     * calendar object
     */
    public Calendar $calendar;

    /**
     * link current day
     */
    public string $currentLink;

    /**
     * @inheritDoc
     */
    public function assignVariables(): void
    {
        parent::assignVariables();

        $eventControllers = \array_filter(
            ObjectTypeCache::getInstance()->getObjectTypes('dev.daries.rp.event.controller'),
            fn($controller) => $controller->getProcessor()->isAccessible()
        );

        WCF::getTPL()->assign([
            'calendar' => $this->calendar->render(),
            'currentLink' => $this->currentLink,
            'eventControllers' => $eventControllers,
            'lastMonthLink' => $this->calendar->getLastMonthLink(),
            'nextMonthLink' => $this->calendar->getNextMonthLink(),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function readData(): void
    {
        $currentDate = new \DateTimeImmutable('now');
        $this->currentLink = LinkHandler::getInstance()->getLink('Calendar', [
            'application' => 'rp',
            'month' => $currentDate->format('n'),
            'year' => $currentDate->format('Y'),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function readParameters(): void
    {
        parent::readParameters();

        try {
            $parameters = Helper::mapQueryParameters(
                $_POST,
                <<<'EOT'
                    array {
                        objectType?: string
                    }
                    EOT
            );

            if (isset($parameters['objectType'])) {
                HeaderUtil::redirect(
                    LinkHandler::getInstance()->getLink(
                        'EventAdd',
                        [
                            'application' => 'rp',
                            'type' => $parameters['objectType']
                        ]
                    )
                );
                exit;
            }

            $queryParameters = Helper::mapQueryParameters(
                $_GET,
                <<<'EOT'
                    array {
                        month?: positive-int,
                        year?: positive-int
                    }
                    EOT
            );

            $month = $queryParameters['month'] ?? DateUtil::format(DateUtil::getDateTimeByTimestamp(TIME_NOW), 'n');
            $year = $queryParameters['year'] ?? DateUtil::format(DateUtil::getDateTimeByTimestamp(TIME_NOW), 'Y');

            if ($month < 1 || $month > 12) throw new IllegalLinkException();
        } catch (MappingError) {
            throw new IllegalLinkException();
        }

        $this->calendar = new Calendar($year, $month);
    }
}
