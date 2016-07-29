<?php

namespace ZoneFlight\Controllers;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use ZoneFlight\Entities\Airport;
use ZoneFlight\Utils\SkyscannerUtils;

class FlightController implements ControllerProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function connect(Application $app)
    {
        /* @var $controllers ControllerCollection */
        $controllers = $app['controllers_factory'];

        $controllers->get('/flights', [$this, 'getFlights']);

        return $controllers;
    }

    public function getFlights(Application $app, Request $req)
    {
        //$params = $req->query->all();

        $mandatory = [
            "country",
            "currency",
            "locale",
            "originplace",
            "destinationplace",
            "outbounddate",
            "adults"
        ];

        $params = [
            "country"          => "FR",
            "currency"         => "EUR",
            "locale"           => "FR",
            "originplace"      => "CDG-sky",
            "destinationplace" => "KIX-sky",
            "outbounddate"     => "2016-09-23",
            "adults"           => 2
        ];

        if (false === SkyscannerUtils::verifyFields($mandatory, $params)) {
            return $app->abort(400, "Missing fields");
        }

        $session_url = SkyscannerUtils::getSession($app, $params);

        $flights = SkyscannerUtils::getFlights($app, $session_url);

        return $app->json($flights, 200);
    }
}
