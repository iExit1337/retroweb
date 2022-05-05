<?php

namespace System\App\Model;

use System\App\Connection;
use System\Config;

class FactoryManager
{

    /**
     * @var AbstractFactoryModel[]
     */
    private $_factoryCache = [];

    /**
     * @var Connection
     */
    private $_connection;

    /**
     * @var Config
     */
    private $_config;

    /**
     * FactoryManager constructor.
     *
     * @param Connection $connection
     * @param Config $config
     */
    public function __construct(Connection $connection, Config $config)
    {

        $this->_connection = $connection;
        $this->_config = $config;
    }

    /**
     * @param string $className
     *
     * @return null|AbstractFactoryModel
     */
    public function get(string $className): ?AbstractFactoryModel
    {
        if (!isset($this->_factoryCache[$className])) {
            $class = new $className($this->_connection, $this, $this->_config);
            if ($class instanceof AbstractFactoryModel) {
                $this->_factoryCache[$className] = $class;
            } else {
                unset($class);

                return null;
            }
        }

        return $this->_factoryCache[$className];
    }
}