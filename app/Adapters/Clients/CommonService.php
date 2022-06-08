<?php

namespace App\Adapters\Clients;

/**
 * Represents methods interacting with the common service.
 */
interface CommonService
{
    /**
     * Get string timezone.
     *
     * @param int $timeZoneGMT
     * @return string
     */
    public function getStringTimeZone(int $timeZoneGMT): string;
}
