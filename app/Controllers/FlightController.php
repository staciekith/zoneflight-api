<?php

namespace ZoneFlight\Controllers;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use ZoneFlight\Entities\Airport;
use ZoneFlight\Utils\SkyscannerUtils;
use ZoneFlight\Utils\FlightsUtils;

class FlightController implements ControllerProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function connect(Application $app)
    {
        /* @var $controllers ControllerCollection */
        $controllers = $app['controllers_factory'];

        $controllers->put('/flights', [$this, 'getFlightsFromAtoB']);

        $controllers->put('/flights/xtox', [$this, 'getFlightsFromXtoX']);

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
        $params = $req->request->all();

        $mandatory = [
            "country",
            "currency",
            "locale",
            "originplace",
            "destinationplace",
            "outbounddate",
            "adults",
        ];

        $params["locationschema"] = "Iata";
        $params["groupPricing"]   = true;
        $params["sorttype"]       = "price";
        $params["sortorder"]      = "asc";

        if (false === SkyscannerUtils::verifyFields($mandatory, $params)) {
            return $app->abort(400, "Missing fields");
        }

        $flights = FlightsUtils::getFlightsPointToPoint($app, $params);

        if (null === $flights) {
            return $app->abort(404, "Flights not found");
        }

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
