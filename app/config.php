<?php

namespace ZoneFlight;

use Silex\Application;

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

    }
}
