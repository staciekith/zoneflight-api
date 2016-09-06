<?php

namespace ZoneFlight\Utils;

use Silex\Application;
use ZoneFlight\Utils\SkyscannerUtils;

class FlightsUtils
{
    /**
     * Get les billets d'un point A vers un point B
     *
     * @param $app      Application
     * @param $params   array
     *
     * @return array|null
     */
    public static function getFlightsPointToPoint(Application $app, array $params)
    {
        $session_url = SkyscannerUtils::getSession($app, $params);
        $flights     = SkyscannerUtils::getFlights($app, $session_url);

        if (null === $flights) {
            return null;
        }

        $flights = SkyscannerUtils::formatResults($flights);

        return $flights;
    }
}
