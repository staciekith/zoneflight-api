<?php

namespace ZoneFlight\Utils;

use Silex\Application;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Promise;

class SkyscannerUtils
{
    /**
     * Récupérer les sessions Skyscanner en asynchrone
     *
     * @param $app           Application
     * @param $base_params   array
     * @param $origins       array
     * @param $destinations  array
     *
     * @return array
     */
    public static function getAsyncSessions(Application $app, array $base_params, array $origins, array $destinations)
    {
        $sessions = [];
        $promises = [];
        $uri      = 'http://partners.api.skyscanner.net/apiservices/pricing/v1.0';
        $client   = new Client();

        $base_params["apiKey"] = $app['skyscanner_api_key'];

        // Get sessions : POST pour chaque paire originplace/destinationplace
        foreach ($origins as $ori) {
            $sessions[$ori] = [];
            foreach ($destinations as $dest) {
                $sessions[$ori][$dest]           = [];
                $base_params["originplace"]      = $ori;
                $base_params["destinationplace"] = $dest;

                $promises[] = $client->postAsync(
                    $uri,
                    [
                        'form_params' => $base_params
                    ]
                )->then(
                    function ($response) use (&$sessions, $ori, $dest) {
                        $sessions[$ori][$dest] = $response->getHeaders()['Location'][0];
                    },
                    function ($exception) use (&$sessions, $ori, $dest) {
                        $sessions[$ori][$dest] = null;
                    }
                );
            }
        }

        Promise\unwrap($promises);

        return $sessions;
    }

    /**
     * Récupérer les vols via Skyscanner en asynchrone
     *
     * @param $app           Application
     * @param $sessions      array
     *
     * @return array
     */
    public static function getAsyncFlights(Application $app, $sessions)
    {
        $flights  = [];
        $promises = [];

        foreach ($sessions as $ori => $session_ori) {
            $flights[$ori] = [];
            foreach ($session_ori as $dest => $session_url) {
                $flights[$ori][$dest] = [];

                if (null === $session_url) {
                    $flights[$ori][$dest] = null;
                    continue;
                }

                $url        = $session_url . "?apiKey={$app['skyscanner_api_key']}";
                $client     = new Client();
                $promises[] = $client->getAsync($url)->then(function ($response) use (&$flights, $ori, $dest) {
                    $flights[$ori][$dest] = json_decode($response->getBody()->getContents(), true);
                });
            }
        }

        Promise\unwrap($promises);

        return $flights;
    }

    /**
     * Récupérer une session Skyscanner
     *
     * @param $app      Application
     * @param $params   array
     *
     * @return string|null
     */
    public static function getSession(Application $app, array $params)
    {
        $params["apiKey"] = $app['skyscanner_api_key'];
        $client = new Client();

        $request  = new Request('POST', 'http://partners.api.skyscanner.net/apiservices/pricing/v1.0');

        try {
            $response = $client->send($request, [
                "form_params" => $params
            ]);
        } catch (\Exception $exception) {
            return null;
        }

        return $response->getHeaders()['Location'][0];
    }

    /**
     * Récupérer les vols via l'API Skyscanner
     *
     * @param $app          Application
     * @param $session_url  string
     *
     * @return array
     */
    public static function getFlights(Application $app, $session_url)
    {
        $client = new Client();

        $url = $session_url . "?apiKey={$app['skyscanner_api_key']}";

        $flights = self::pullSession($client, $url);

        // Pull session until Status is "UpdatesComplete"
        while ("UpdatesComplete" !== $flights['Status'])
        {
            $flights = self::pullSession($client, $url);
            sleep(2);
        }

        return $flights;
    }

    /**
     * Faire une requête à l'API Skyscanner
     *
     * @param $client       Client
     * @param $session_url  string
     *
     * @return array
     */
    public static function pullSession(Client $client, $session_url)
    {
        $request  = new Request('GET', $session_url);
        $response = $client->send($request);
        $flights  = json_decode($response->getBody()->getContents(), true);

        return $flights;
    }

    /**
     * Vérifie les paramètres entrés
     *
     * @param $mandatory    array
     * @param $fields       array
     *
     * @return boolean
     */
    public static function verifyFields(array $mandatory, array $fields)
    {
        $missing_fields = array_diff($mandatory, array_keys($fields));

        if (0 !== count($missing_fields)) {
            return false;
        }

        return true;
    }

    /**
     * Formatte les données reçues de l'API Skyscanner
     *
     * @param $results    array
     *
     * @return array
     */
    public static function formatResults(array $results)
    {
        $formatted     = [];
        $pre_formatted = [];
        $itineraries   = $results["Itineraries"];
        $legs          = $results["Legs"];
        $airports      = $results["Places"];

        // On récupère tous les itinéraires et les legs (trajets)
        for ($i = 0; $i < count($legs); $i++) {
            $pre_formatted[] = [
                "itinerary" => $itineraries[$i],
                "leg"       => $legs[$i]
            ];
        }

        // Pour chaque itinéraire, on filtre les informations pour ne récupérer que
        // - l'id de l'itinéraire aller
        // - l'id de l'itinéraire retour
        // - les informations pour la réservation
        // - le prix
        foreach ($pre_formatted as $item) {
            $formatted[] = [
                "outbound_id" => $item["itinerary"]["OutboundLegId"],
                "inbound_id"  => empty($item["itinerary"]["InboundLegId"]) ? null : $item["itinerary"]["InboundLegId"],
                "booking"     => $item["itinerary"]["BookingDetailsLink"],
                "price"       => $item["itinerary"]["PricingOptions"][0]["Price"]
            ];
        }

        foreach ($formatted as &$item) {
            // Pour chaque itinéraire, on récupère le trajets aller-retour associés
            $inbound  = null === $item["inbound_id"] ? null : self::findLegFromId($legs, $item["inbound_id"]);
            $outbound = null === $item["outbound_id"] ? null : self::findLegFromId($legs, $item["outbound_id"]);

            // On récupère les informations du trajet retour
            if (null !== $inbound) {
                $item["inbound"] = self::getLegInformation($inbound, $airports);
            }

            // On récupère les informations du trajet aller
            if (null !== $outbound) {
                $item["outbound"] = self::getLegInformation($outbound, $airports);
            }
        }

        return $formatted;
    }

    /**
     * Récupère les informations d'un trajet donné
     *
     * @param $leg          array
     * @param $airports     array
     *
     * @return array
     */
    private static function getLegInformation($leg, $airports)
    {
        $leg_informations = [
            "direction"   => $leg["Directionality"],
            "origin"      => $leg["OriginStation"],
            "destination" => $leg["DestinationStation"],
            "departure"   => $leg["Departure"],
            "arrival"     => $leg["Arrival"],
            "stops"       => count($leg["Stops"])
        ];
        $leg_informations["origin"]      = self::findAirportFromId($airports, $leg_informations["origin"]);
        $leg_informations["destination"] = self::findAirportFromId($airports, $leg_informations["destination"]);

        return $leg_informations;
    }

    /**
     * Trouve l'aéroport via son id renvoyé par Skyscanner
     *
     * @param $id          integer
     * @param $places      array
     *
     * @return array
     */
    private static function findAirportFromId(array $places, $id)
    {
        foreach ($places as $place) {
            if ($place["Id"] === $id) {
                return $place;
            }
        }

        return [];
    }

    /**
     * Trouve le leg via son id renvoyé par Skyscanner
     *
     * @param $id          string
     * @param $legs        array
     *
     * @return array
     */
    private static function findLegFromId(array $legs, $id)
    {
       foreach ($legs as $leg) {
            if ($leg["Id"] === $id) {
                return $leg;
            }
        }

        return [];
    }
}
