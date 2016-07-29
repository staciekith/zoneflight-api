#!/usr/bin/env php
<?php

use ZoneFlight\Entities\Airport;

$app = require __DIR__ . "/../public/index.php";

$file = file_get_contents( __DIR__ . "/airports.json");
$airports = json_decode($file, true);

foreach ($airports as $airport) {

}
