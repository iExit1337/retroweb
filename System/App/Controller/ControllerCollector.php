<?php

namespace System\App\Controller;

use System\App\App;
use System\HTTP\IRoutable;

class ControllerCollector
{

    /**
     * @var Controller[]
     */
    private $_controllers = [];

    /**
     * @var Controller[]
     */
    private $_routableControllers = [];

    /**
     * @var App
     */
    private $_app;

    /**
     * ControllerCollector constructor.
     *
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->_app = $app;
    }

    /**
     * @param string $controllerClass
     *
     * @return Controller|null
     */
    public function get(string $controllerClass): ?Controller
    {
        if (isset($this->_controllers[$controllerClass])) {
            return $this->_controllers[$controllerClass];
        }

        return null;
    }

    /**
     * @param string $controllerClass
     */
    public function add(string $controllerClass): void
    {
        /**
         * @var $controller Controller
         */
        $controller = new $controllerClass($this->_app);
        $controller->onRegistration();
        $this->addController($controllerClass, $controller);
    }

    /**
     * @param string $className
     * @param Controller $controller
     */
    private function addController(string $className, Controller $controller): void
    {
        $this->_controllers[$className] = $controller;
        if ($controller instanceof IRoutable) {
            $this->_routableControllers[] = $controller;
        }
    }

    /**
     * @return IRoutable[]
     */
    public function getRoutableControllers(): array
    {
        return $this->_routableControllers;
    }
}