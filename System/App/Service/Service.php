<?php

namespace System\App\Service;

use System\App\App;

abstract class Service
{

    /**
     * @var App
     */
    private $_app;

    /**
     * Service constructor.
     *
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->_app = $app;

        $this->onConstruction();
    }

    /**
     * @return App
     */
    public function getApp(): App
    {
        return $this->_app;
    }

    abstract public function onConstruction(): void;
}