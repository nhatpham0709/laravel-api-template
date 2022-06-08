<?php

namespace App\Adapters\Clients;

use App\Constants\Common;
use DateTimeZone;

/**
 * Represents methods interacting with the common service.
 */
class CommonServiceImpl implements CommonService
{
    /**
     * Get string timezone.
     *
     * @param int $timeZoneGMT
     * @return string
     */
    public function getStringTimeZone(int $timeZoneGMT): string
    {
        $timezones = array();
        $timeZoneString = config('app.timezone');
        foreach (DateTimeZone::listAbbreviations() as $listTimeZone) {
            $timezones = array_merge($timezones, $listTimeZone);
        }

        foreach ($timezones as $timeZone) {
            $offset = $timeZone['offset'] / Common::NUM_SECOND_IN_HOUR;
            if (intval($offset) === $timeZoneGMT && $timeZone['timezone_id']) {
                $timeZoneString = $timeZone['timezone_id'];
                break;
            }
        }
        return $timeZoneString;
    }
}
