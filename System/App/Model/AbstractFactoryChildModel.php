<?php

namespace System\App\Model;

use System\App\Connection;
use System\Config;

class AbstractFactoryChildModel
{

    /**
     * @var AbstractFactoryModel
     */
    private $_parent;

    /**
     * @var FactoryManager
     */
    private $_factoryManager;

    /**
     * @var array
     */
    private $_row;

    /**
     * @var Config
     */
    private $_config;

    /**
     * AbstractFactoryChildModel constructor.
     *
     * @param AbstractFactoryModel $parent
     * @param FactoryManager $factoryManager
     * @param array $row
     * @param Config $config
     */
    public function __construct(AbstractFactoryModel $parent, FactoryManager $factoryManager, array $row, Config $config)
    {
        $this->_parent = $parent;
        $this->_factoryManager = $factoryManager;
        $this->_row = $row;
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
     * @param string $var
     *
     * @return mixed|null
     */
    public function __get(string $var)
    {
        return $this->get($var);
    }

    /**
     * @param string $var
     *
     * @return mixed|null
     */
    public function get(string $var)
    {
        if (!isset($this->_row[$var])) {

            $queryString = "SELECT `{$var}` FROM `{$this->_parent->_getTable()}` WHERE `id` = :id";
            try {
                $query = $this->_parent->getConnection()->getPDO()->prepare($queryString);
                $query->execute([':id' => $this->getInt("id")]);

                if ($query->rowCount() > 0) {
                    $row = $query->fetchObject();
                    $this->_row[$var] = $row->$var;
                } else {
                    return null;
                }
            } catch (\PDOException $e) {
                exit($e->getMessage());
            }
        }

        return $this->_row[$var];
    }

    /**
     * @param string $var
     *
     * @return int
     */
    public function getInt(string $var): int
    {
        return (int)$this->get($var);
    }

    /**
     * @return bool
     */
    public function delete(): bool
    {
        $query = $this->getConnection()
            ->getPDO()
            ->prepare("DELETE FROM `{$this->_parent->_getTable()}` WHERE `id` = :id");
        try {
            $query->execute([':id' => $this->getInt("id")]);

            return true;
        } catch (\PDOException $e) {
            echo $e->getMessage();

            return false;
        }
    }

    /**
     * @param string $column
     * @param mixed $value
     *
     * @return bool
     */
    public function set(string $column, $value): bool
    {
        $query = $this->getConnection()
            ->getPDO()
            ->prepare("UPDATE `{$this->_parent->_getTable()}` SET `{$column}` = :value WHERE `id` = :id");
        try {
            $query->execute([
                ':value' => $value,
                ':id' => $this->getInt("id")
            ]);

            $this->_row[$column] = $value;

            return true;
        } catch (\PDOException $e) {
            echo $e->getMessage();

            return false;
        }
    }

    /**
     * @return FactoryManager
     */
    public function getFactoryManager(): FactoryManager
    {
        return $this->_factoryManager;
    }

    /**
     * @return AbstractFactoryModel
     */
    public function getParent(): AbstractFactoryModel
    {
        return $this->_parent;
    }

    /**
     * @return Connection
     */
    public function getConnection(): Connection
    {
        return $this->_parent->getConnection();
    }
}