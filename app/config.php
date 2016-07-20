<?php

namespace ZoneFlight;

use Silex\Application;
use Silex\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

use Saxulum\Console\Provider\ConsoleProvider;
use Saxulum\DoctrineOrmManagerRegistry\Provider\DoctrineOrmManagerRegistryProvider;

/**
 * Configuration principale de l'application
 */
class Config implements ServiceProviderInterface
{
    private $env = "production";

    public function __construct($env = null)
    {
        if (null !== $env) {
            $this->env = $env;
            if (true === file_exists(__DIR__ . "/env/{$this->env}.php")) {
                require_once __DIR__ . "/env/{$this->env}.php";
            }
        }
    }

    /**
     * @{inherit doc}
     */
    public function register(Container $app)
    {
        $this->registerServiceProviders($app);
        $this->registerRoutes($app);
    }

    /**
     * Register Silex service providers
     *
     * @param  Application $app Silex Application
     */
    private function registerServiceProviders(Application $app)
    {
        $app->register(new Provider\ServiceControllerServiceProvider());
        $app->register(new ConsoleProvider());
        $app->register(new DoctrineOrmManagerRegistryProvider());
    }

    /**
     * Mount all controllers and routes
     * Set corresponding endpoints on the controller classes
     *
     * @param  Application $app Silex Application
     *
     */
    private function registerRoutes(Application $app)
    {
        // Recherche tous les controllers pour les loader dans $app
        foreach (glob(__DIR__ . "/Controllers/*.php") as $controller_name) {
            $controller_name = pathinfo($controller_name)["filename"];
            $class_name      = "\\ZoneFlight\\Controllers\\{$controller_name}";
            if (class_exists($class_name)
                && in_array("Silex\Api\ControllerProviderInterface", class_implements($class_name))
            ) {
                $app[$controller_name] = function () use ($class_name) {
                    return new $class_name();
                };
                $app->mount('/', $app[$controller_name]);
            }
        }
    }
}
