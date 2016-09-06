# zoneflight-api
ZoneFlight API (back)

[![Build Status](https://travis-ci.org/staciekith/zoneflight-api.png)](https://travis-ci.org/staciekith/zoneflight-api)

Installation :
```
composer install
```

Réinstallation (après moidification des dépendances dans `composer.json`) :
```
rm -rf vendor/ composer.lock
composer install
```

Pour lancer le serveur php en local :
```
php -S localhost:8080 -t public public/index.php
```


On accède via :
```
http://localhost:8080/
```



POUR LA DATABASE :
Une fois la database `zoneflight` créée, il faut :
- créer la table `Airport` : /bin/database.sql
- lancer le script `/bin/import_airports.php` avec `APPLICATION_ENV=development ./bin/import_airports.php`

--------------------------------------------------------------------------------------------------------------

Formats de données :

- PUT sur /airports/circle : (aéroport dans un cercle)
    Request :
    ```
    [
        "lon", "lat", "rad"
    ]```
    Response :
    ```
    [
        {
            "id": 1127,
            "name": "Tokyo International Airport",
            "airport_code": "HND",
            "lon": 139.771,
            "lat": 35.5533,
            "country": "Japan",
            "state": "Tokyo Prefecture",
            "city": "Tokyo",
            "timezone": "Asia\/Tokyo"
        },
        {
            "id": 2057,
            "name": "Narita International Airport",
            "airport_code": "NRT",
            "lon": 140.389,
            "lat": 35.7491,
            "country": "Japan",
            "state": "Chiba Prefecture",
            "city": "Narita-shi",
            "timezone": "Asia\/Tokyo"
        }
    ]
    ```

- PUT sur /airports/flights :
    Request :
    ```
    [
        "country", "currency", "locale", "originplace", "destinationplace",
        "outbounddate", "inbounddate", "adults", "children", "infants", "cabinclass"
    ]
    ```
    Response :
    ```
    [
        {
            "outbound_id": "10413-1610231215-CA-1-13068-1610241210",
            "inbound_id": "13068-1612230900-CA-1-10413-1612231745",
            "booking": {
                "Uri": "\/apiservices\/pricing\/v1.0\/d3b8c08a5ff54472b52a7da05237d025_ecilpojl_DCE634A426CBDA30CE7EA3E9068CD053\/booking",
                "Body": "OutboundLegId=10413-1610231215-CA-1-13068-1610241210\u0026InboundLegId=13068-1612230900-CA-1-10413-1612231745",
                "Method": "PUT"
            },
            "price": 1029.58,
            "inbound": {
                "direction": "Inbound",
                "origin": {
                    "Id": 13068,
                    "ParentId": 5965,
                    "Code": "KIX",
                    "Type": "Airport",
                    "Name": "Osaka Kansai International"
                },
                "destination": {
                    "Id": 10413,
                    "ParentId": 6073,
                    "Code": "CDG",
                    "Type": "Airport",
                    "Name": "Paris Charles-de-Gaulle"
                },
                "departure": "2016-12-23T09:00:00",
                "arrival": "2016-12-23T17:45:00",
                "stops": 1
            },
            "outbound": {
                "direction": "Outbound",
                "origin": {
                    "Id": 10413,
                    "ParentId": 6073,
                    "Code": "CDG",
                    "Type": "Airport",
                    "Name": "Paris Charles-de-Gaulle"
                },
                "destination": {
                    "Id": 13068,
                    "ParentId": 5965,
                    "Code": "KIX",
                    "Type": "Airport",
                    "Name": "Osaka Kansai International"
                },
                "departure": "2016-10-23T12:15:00",
                "arrival": "2016-10-24T12:10:00",
                "stops": 1
            }
        },
        {
            "outbound_id": "10413-1610231215-CA-1-13068-1610241210",
            "inbound_id": "13068-1612231600-CA-1-10413-1612240625",
            "booking": {
                "Uri": "\/apiservices\/pricing\/v1.0\/d3b8c08a5ff54472b52a7da05237d025_ecilpojl_DCE634A426CBDA30CE7EA3E9068CD053\/booking",
                "Body": "OutboundLegId=10413-1610231215-CA-1-13068-1610241210\u0026InboundLegId=13068-1612231600-CA-1-10413-1612240625",
                "Method": "PUT"
            },
            "price": 1029.58,
            "inbound": {
                "direction": "Inbound",
                "origin": {
                    "Id": 13068,
                    "ParentId": 5965,
                    "Code": "KIX",
                    "Type": "Airport",
                    "Name": "Osaka Kansai International"
                },
                "destination": {
                    "Id": 10413,
                    "ParentId": 6073,
                    "Code": "CDG",
                    "Type": "Airport",
                    "Name": "Paris Charles-de-Gaulle"
                },
                "departure": "2016-12-23T16:00:00",
                "arrival": "2016-12-24T06:25:00",
                "stops": 1
            },
            "outbound": {
                "direction": "Outbound",
                "origin": {
                    "Id": 10413,
                    "ParentId": 6073,
                    "Code": "CDG",
                    "Type": "Airport",
                    "Name": "Paris Charles-de-Gaulle"
                },
                "destination": {
                    "Id": 13068,
                    "ParentId": 5965,
                    "Code": "KIX",
                    "Type": "Airport",
                    "Name": "Osaka Kansai International"
                },
                "departure": "2016-10-23T12:15:00",
                "arrival": "2016-10-24T12:10:00",
                "stops": 1
            }
        },
    ]
    ```
