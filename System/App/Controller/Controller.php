<?php

namespace System\App\Controller;

use System\App\App;
use System\App\Model\FactoryManager;
use System\App\Service\ServiceCollector;
use System\App\View\View;
use System\Session\Session;

class Controller
{

    /**
     * @var App
     */
    protected $_app;

    /**
     * Controller constructor.
     *
     * @param App $app
     */
    public function __construct(App $app)
    {

        $this->_app = $app;
    }

    public function onRegistration(): void
    {
    }

    /**
     * @return App
     */
    protected function getApp(): App
    {

        return $this->_app;
    }

    /**
     * @return ServiceCollector
     */
    public function getServiceCollector(): ServiceCollector
    {
        return $this->_app->getServiceCollector();
    }

    /**
     * @return Session
     */
    public function getSession(): Session
    {
        return $this->_app->getSession();
    }

    /**
     * @return View
     */
    protected function getView(): View
    {
        return $this->_app->getView();
    }

    /**
     * @return FactoryManager
     */
    protected function getFactoryManager(): FactoryManager
    {
        return $this->_app->getFactoryManager();
    }
}