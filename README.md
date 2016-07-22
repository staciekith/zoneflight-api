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
