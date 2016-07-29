<?php

namespace ZoneFlight\Controllers;

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
        $response = [
            "name"    => "airports",
            "content" => "hello, it's airtport controller",
            "other"   => [
                "other_content"       => "other_content_msg",
                "other_content_again" => "other_content_again_msg"
            ]
        ];

        return $app->json($response, 200);
    }
}
