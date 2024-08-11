<?php

namespace rp\util;

use wcf\system\WCF;

/**
 * ontains raidplaner-related functions.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class RPUtil
{
    /**
     * Formats the points
     */
    public static function formatPoints(float|int $points = 0): string
    {
        $precision = RP_ROUND_POINTS ? RP_ROUND_POINTS_PRECISION : 2;
        $locale = WCF::getLanguage()->getLocale();
        $formatter = new \NumberFormatter($locale, \NumberFormatter::DECIMAL);
        $formatter->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, $precision);
        $formatter->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, $precision);

        return $formatter->format($points);
    }
}
