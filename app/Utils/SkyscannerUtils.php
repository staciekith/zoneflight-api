<?php

namespace ZoneFlight\Utils;

use Silex\Application;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class SkyscannerUtils
{
    /**
     * Récupérer une session Skyscanner
     *
     * @param $app      Application
     * @param $params   array
     *
     * @return string
     */
    public static function getSession(Application $app, array $params)
    {
        $params["apiKey"] = $app['skyscanner_api_key'];

        $client = new Client();

        $request  = new Request('POST', 'http://partners.api.skyscanner.net/apiservices/pricing/v1.0');
        $response = $client->send($request, [
            "form_params" => $params
        ]);

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
                $item["inbound"] = [
                    "direction"   => $inbound["Directionality"],
                    "origin"      => $inbound["OriginStation"],
                    "destination" => $inbound["DestinationStation"],
                    "departure"   => $inbound["Departure"],
                    "arrival"     => $inbound["Arrival"],
                    "stops"       => count($inbound["Stops"])
                ];
                $item["inbound"]["origin"]      = self::findAirportFromId($airports, $item["inbound"]["origin"]);
                $item["inbound"]["destination"] = self::findAirportFromId($airports, $item["inbound"]["destination"]);
            }

            // On récupère les informations du trajet aller
            if (null !== $outbound) {
                $item["outbound"] = [
                    "direction"   => $outbound["Directionality"],
                    "origin"      => $outbound["OriginStation"],
                    "destination" => $outbound["DestinationStation"],
                    "departure"   => $outbound["Departure"],
                    "arrival"     => $outbound["Arrival"],
                    "stops"       => count($outbound["Stops"])
                ];
                $item["outbound"]["origin"]      = self::findAirportFromId($airports, $item["outbound"]["origin"]);
                $item["outbound"]["destination"] = self::findAirportFromId($airports, $item["outbound"]["destination"]);
            }
        }

        return $formatted;
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
