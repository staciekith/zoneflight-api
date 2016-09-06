<?php

namespace ZoneFlight\Utils;

use Silex\Application;

class DistanceUtils
{
    /**
     * Calcule la distance entre deux coordonnées [lon, lat] en KM
     *
     * @param $form    array
     * @param $to      array
     *
     * @return float
     */
    public static function distance($from, $to)
    {
        $theta = $from["lon"] - $to["lon"];
        $dist  = sin(deg2rad($from["lat"])) * sin(deg2rad($to["lat"])) +  cos(deg2rad($from["lat"])) * cos(deg2rad($to["lat"])) * cos(deg2rad($theta));
        $dist  = acos($dist);
        $dist  = rad2deg($dist);
        $dist  = $dist * 60 * 1.1515 * 1.609344;

        return $dist;
    }
}
