<?php

namespace Zoneflight\Utils;

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

        $request  = new Request('GET', $url);
        $response = $client->send($request);

        $flights = json_decode($response->getBody()->getContents(), true);

        // Poll session until Status is "UpdatesComplete"
        while ("UpdatesPending" === $flights['Status'])
        {
            $response = $client->send($request);
            $flights = json_decode($response->getBody()->getContents(), true);
        }

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
        for ($i = 0; $i < count($itineraries); $i++) {
            $pre_formatted[] = [
                "itinerary" => $itineraries[$i],
                "leg"       => $legs[$i]
            ];
        }
        foreach ($pre_formatted as $item) {
            $formatted[] = [
                "id"          => $item["itinerary"]["OutboundLegId"],
                "booking"     => $item["itinerary"]["BookingDetailsLink"],
                "direction"   => $item["leg"]["Directionality"],
                "origin"      => $item["leg"]["OriginStation"],
                "destination" => $item["leg"]["DestinationStation"],
                "departure"   => $item["leg"]["Departure"],
                "arrival"     => $item["leg"]["Arrival"],
                "stops"       => count($item["leg"]["Stops"]),
                "price"       => $item["itinerary"]["PricingOptions"][0]["Price"]
            ];
        }
        foreach ($formatted as &$item) {
            $origin              = self::findAirportFromId($airports, $item["origin"]);
            $dest                = self::findAirportFromId($airports, $item["destination"]);
            $item["origin"]      = $origin;
            $item["destination"] = $dest;
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
}
