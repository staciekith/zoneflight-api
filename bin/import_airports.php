#!/usr/bin/env php
<?php

use ZoneFlight\Entities\Airport;

$app = require __DIR__ . "/../public/index.php";

$file = file_get_contents( __DIR__ . "/airports.json");
$airports = json_decode($file, true);

foreach ($airports as $airport) {

    if ("Airports" !== $airport["type"]) {
        continue;
    }

    $new_airport = new Airport();
    $new_airport->setName($airport["name"]);
    $new_airport->setAirportCode($airport["code"]);
    $new_airport->setLon(floatval($airport["lon"]));
    $new_airport->setLat(floatval($airport["lat"]));
    $new_airport->setCountry($airport["country"]);
    $new_airport->setState($airport["state"]);
    $new_airport->setCity($airport["city"]);
    $new_airport->setTimezone($airport["tz"]);

    $app["orm.em"]->persist($new_airport);
}

$app["orm.em"]->flush();

return;
