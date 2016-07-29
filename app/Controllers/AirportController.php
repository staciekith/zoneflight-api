<?php

namespace ZoneFlight\Controllers;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use ZoneFlight\Entities\Airport;

class AirportController implements ControllerProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function connect(Application $app)
    {
        /* @var $controllers ControllerCollection */
        $controllers = $app['controllers_factory'];

        $controllers->get('/airports', [$this, 'getAirports']);

        $controllers->get('/airports/{airport}', [$this, 'getAirport'])
                    ->assert("airport", "\d+")
                    ->convert("airport", $app["findOneOr404"]('Airport', 'id'));

        return $controllers;
    }

    public function getAirports(Application $app)
    {
        $airports = $app["repositories"]("Airport")->findAll();

        return $app->json($airports, 200);
    }

    public function getAirport(Application $app, Airport $airport)
    {
        return $app->json($airport, 200);
    }
}
