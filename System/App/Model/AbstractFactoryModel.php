<?php

namespace System\App\Model;

use System\App\Connection;
use System\Config;

abstract class AbstractFactoryModel
{

    /**
     * @var AbstractFactoryChildModel[]
     */
    private $_objectCache = [];

    /**
     * @var FactoryManager
     */
    protected $_factoryManager;

    /**
     * @var Connection
     */
    protected $_connection;

    /**
     * @var Config
     */
    private $_config;

    /**
     * @return string
     */
    abstract protected function getTable(): string;

    /**
     * @return bool
     */
    abstract protected function hasChildren(): bool;

    /**
     * @return string
     */
    abstract protected function getChildrenInstance(): string;

    /**
     * @return string
     */
    public function _getTable(): string
    {
        return $this->getTable();
    }

    /**
     * AbstractFactoryModel constructor.
     *
     * @param Connection $connection
     * @param FactoryManager $factoryManager
     * @param Config $config
     */
    public function __construct(Connection $connection, FactoryManager $factoryManager, Config $config)
    {
        $this->_factoryManager = $factoryManager;
        $this->_connection = $connection;
        $this->_config = $config;
    }

    /**
     * @return FactoryManager
     */
    public function getFactoryManager(): FactoryManager
    {
        return $this->_factoryManager;
    }

    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->_config;
    }

    /**
     * @param int $id
     *
     * @return null|AbstractFactoryChildModel
     */
    public function getById(int $id): ?AbstractFactoryChildModel
    {

        return $this->getByColumn("id", $id);
    }

    /**
     * @param string $column
     * @param                $value
     * @param null|\stdClass $object
     *
     * @return bool
     */
    protected function isInCache(string $column, $value, ?\stdClass &$object): bool
    {
        foreach ($this->_objectCache as $cacheObject) {
            if ($cacheObject->get($column) == $value) {
                $object = $cacheObject;

                return true;
            }
        }

        return false;
    }

    /**
     * @param \stdClass $row
     *
     * @return null|AbstractFactoryChildModel
     * @throws \Exception
     */
    public function getByRow(\stdClass $row): ?AbstractFactoryChildModel
    {
        if (!isset($row->id)) {
            throw new \Exception('getByRow needs `id`');
        }

        if ($this->isInCache('id', $row->id, $object)) {
            return $object;
        }

        return $this->createCacheObject($row);
    }

    /**
     * @param string $column
     * @param        $value
     *
     * @return null|AbstractFactoryChildModel
     */
    public function getByColumn(string $column, $value): ?AbstractFactoryChildModel
    {
        if (!$this->hasChildren()) {
            return null;
        }

        if ($this->isInCache($column, $value, $object)) {
            return $object;
        }

        if (strtolower($column) != "id") {
            $columns = [
                "`id`",
                "`$column`"
            ];
        } else {
            $columns = ["`id`"];
        }

        $columns = implode(',', $columns);
        $queryString = "SELECT $columns FROM `{$this->getTable()}` WHERE `$column` = :value";

        try {
            $query = $this->_connection->getPDO()->prepare($queryString);
            $query->execute([':value' => $value]);

            if ($query->rowCount() > 0) {
                return $this->createCacheObject($query->fetchObject());
            }
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }

        return null;
    }

    /**
     * @param array $data
     *
     * @return null|AbstractFactoryChildModel
     */
    public function createObject(array $data): ?AbstractFactoryChildModel
    {
        $queryString = "INSERT INTO `{$this->getTable()}` SET ";
        $params = [];
        foreach ($data as $column => $value) {
            $queryString .= "`{$column}` = :{$column}, ";
            $params[':' . $column] = $value;
        }

        $pdo = $this->getConnection()->getPDO();

        $queryString = substr($queryString, 0, strlen($queryString) - 2);
        $query = $pdo->prepare($queryString);
        try {
            $query->execute($params);

            return $this->getById($pdo->lastInsertId());
        } catch (\PDOException $e) {
            echo $e->getMessage();

            return null;
        }
    }

    /**
     * @param \stdClass $row
     *
     * @return AbstractFactoryChildModel
     */
    protected function createCacheObject(\stdClass $row): AbstractFactoryChildModel
    {
        $className = $this->getChildrenInstance();
        $object = new $className($this, $this->_factoryManager, (array)$row, $this->_config);

        $this->_objectCache[] = $object;

        return $object;
    }

    /**
     * @return Connection
     */
    public function getConnection(): Connection
    {
        return $this->_connection;
    }
}