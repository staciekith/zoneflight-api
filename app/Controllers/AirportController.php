<?php

namespace ZoneFlight\Controllers;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use ZoneFlight\Entities\Airport;
use ZoneFlight\Utils\DistanceUtils;

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

        $controllers->put('/airports/circle', [$this, 'getAirportForCircle']);

        return $controllers;
    }

    /**
     * Récupérer tous les aéroports de la base
     *
     * @param $app      Application
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getAirports(Application $app)
    {
        $airports = $app["repositories"]("Airport")->findAll();

        return $app->json($airports, 200);
    }

    /**
     * Récupérer un aéroport via son id
     *
     * @param $app      Application
     * @param $airport  Airport
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getAirport(Application $app, Airport $airport)
    {
        return $app->json($airport, 200);
    }

    /**
     * Récupérer les aéroports dans un cercle (rayon en KM)
     *
     * @param $app      Application
     * @param $req      Request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getAirportForCircle(Application $app, Request $req)
    {
        $datas          = $req->request->all();
        $mandatory      = [ "lon", "lat", "rad" ];
        $missing_fields = array_diff($mandatory, array_keys($datas));

        if (0 !== count($missing_fields)) {
            return $app->abort(400, "Missing fields");
        }

        $from = [
            "lon" => $datas["lon"],
            "lat" => $datas["lat"]
        ];

        $all_airports = $app["repositories"]("Airport")->findAll();
        $airports_in  = [];
        foreach ($all_airports as $airport) {
            $to = [
                "lon" => $airport->getLon(),
                "lat" => $airport->getLat()
            ];
            $distance = DistanceUtils::distance($from, $to);
            if ($distance < $datas["rad"]) {
                $airports_in[] = $airport->toArray();
            }
        }

        return $app->json($airports_in, 200);
    }
}
