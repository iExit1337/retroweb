<?php

namespace System\App;

use System\App\Model\FactoryManager;
use System\App\Service\ServiceCollector;
use System\App\View\View;
use System\Config;
use System\Session\Session;

class App
{

    /**
     * @var View
     */
    private $_view;

    /**
     * @var FactoryManager
     */
    private $_factoryManager;

    /**
     * @var Session
     */
    private $_session;

    /**
     * @var Config
     */
    private $_config;

    /**
     * @var ServiceCollector
     */
    private $_serviceCollector;

    /**
     * @param ServiceCollector $serviceCollector
     */
    public function setServiceCollector(ServiceCollector $serviceCollector): void
    {
        $this->_serviceCollector = $serviceCollector;
    }

    /**
     * @return ServiceCollector
     */
    public function getServiceCollector(): ServiceCollector
    {
        return $this->_serviceCollector;
    }

    /**
     * @param View $view
     */
    public function setView(View $view): void
    {
        $this->_view = $view;
    }

    /**
     * @return View
     */
    public function getView(): View
    {
        return $this->_view;
    }

    /**
     * @param Config $config
     */
    public function setConfig(Config $config): void
    {
        $this->_config = $config;
    }

    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->_config;
    }

    /**
     * @param FactoryManager $factoryManager
     */
    public function setFactoryManager(FactoryManager $factoryManager): void
    {
        $this->_factoryManager = $factoryManager;
    }

    /**
     * @return FactoryManager
     */
    public function getFactoryManager(): FactoryManager
    {
        return $this->_factoryManager;
    }

    /**
     * @param Session $session
     */
    public function setSession(Session $session): void
    {
        $this->_session = $session;
    }

    /**
     * @return Session
     */
    public function getSession(): Session
    {
        return $this->_session;
    }
}