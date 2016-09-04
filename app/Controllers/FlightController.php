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

        $controllers->get('/flights', [$this, 'getFlightsFromAtoB']);

        $controllers->get('/flights/xtox', [$this, 'getFlightsFromXtoX']);

        return $controllers;
    }

    /**
     * Récupérer les vols d'un point A vers un point B
     *
     * @param $app      Application
     * @param $req      Request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getFlightsFromAtoB(Application $app, Request $req)
    {
        //$params = $req->request->all();

        $mandatory = [
            "country",
            "currency",
            "locale",
            "originplace",
            "destinationplace",
            "outbounddate",
            "adults",
        ];

        $params = [
            "country"          => "FR",
            "currency"         => "EUR",
            "locale"           => "FR",
            "originplace"      => "CDG-sky",
            "destinationplace" => "KIX-sky",
            "outbounddate"     => "2016-10-23",
            //"inbounddate"      => "2016-12-23",
            "adults"           => 2,
            "groupPricing"     => true
        ];

        $params['locationschema'] = "Iata";

        if (false === SkyscannerUtils::verifyFields($mandatory, $params)) {
            return $app->abort(400, "Missing fields");
        }

        $session_url = SkyscannerUtils::getSession($app, $params);

        $flights = SkyscannerUtils::getFlights($app, $session_url);

        return $app->json($flights, 200);
    }

    /**
     * Récupérer les vols de X points vers X points
     *
     * @param $app      Application
     * @param $req      Request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getFlightsFromXtoX(Application $app, Request $req)
    {
        return $app->json("ok", 200);
    }

}
