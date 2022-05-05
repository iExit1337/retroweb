<?php

namespace System\App\Service;

use System\App\App;

class ServiceCollector
{

    /**
     * @var Service
     */
    private $_services = [];

    /**
     * @var App
     */
    private $_app;

    /**
     * ServiceCollector constructor.
     *
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->_app = $app;
    }

    /**
     * @param string $serviceClass
     *
     * @return Service
     */
    public function get(string $serviceClass): Service
    {
        if (!isset($this->_services[$serviceClass])) {
            $this->add($serviceClass);
        }

        return $this->_services[$serviceClass];
    }

    /**
     * @param string $serviceClass
     */
    public function add(string $serviceClass): void
    {
        /**
         * @var Service $service
         */
        $service = new $serviceClass($this->_app);
        $this->_services[$serviceClass] = $service;
    }
}