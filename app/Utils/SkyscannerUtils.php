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
    public static function getSession(Application $app, $params)
    {
        $params["apiKey"] = $app['skyscanner_api_key'];

        $client = new Client();

        $request  = new Request('POST', 'http://partners.api.skyscanner.net/apiservices/pricing/v1.0');
        $response = $client->send($request, [
            "form_params" => $params
        ]);

        return $response->getHeaders()["Location"][0];
    }

    /**
     * Récupérer les vols via l'API Skyscanner
     *
     * @param $app          Application
     * @param $session_url  string
     *
     * @return JSON
     */
    public static function getFlights(Application $app, $session_url)
    {
        $client = new Client();

        $url = $session_url . "?apiKey={$app['skyscanner_api_key']}";

        $request  = new Request('GET', $url);
        $response = $client->send($request);

        $flights = $response->getBody()->getContents();

        return json_decode($flights, true);
    }

    // A voir après si on ne fait pas une entité Session et des validators symfony
    /**
     * Vérifie les paramètres entrés
     *
     * @param $mandatory    array
     * @param $fields       array
     *
     * @return boolean
     */
    public static function verifyFields($mandatory, $fields)
    {
        $missing_fields = array_diff($mandatory, array_keys($fields));

        if (0 !== count($missing_fields)) {
            return false;
        }

        return true;
    }
}
