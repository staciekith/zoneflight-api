<?php

namespace ZoneFlight;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

        return $controllers;
    }

    public function getAirports(Application $app)
    {
        return $app->json("ok", 200);
    }
}
