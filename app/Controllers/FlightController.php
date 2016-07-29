<?php

namespace ZoneFlight\Controllers;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use ZoneFlight\Entities\Airport;

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
        return $app->json("ok", 200);
    }
}
