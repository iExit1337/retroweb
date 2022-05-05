<?php


namespace System\App;


class Connection
{

    /**
     * @var \PDO
     */
    private $_pdo;

    /**
     * Connection constructor.
     *
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        $this->_pdo = $pdo;
    }

    /**
     * @return \PDO
     */
    public function getPDO(): \PDO
    {
        return $this->_pdo;
    }

    /**
     * @param callable $callback
     * @param array $params
     *
     * @return bool
     */
    public function makeTransaction(callable $callback, array $params = []): bool
    {
        $args = [$this->_pdo];
        foreach ($params as $param) {
            $args[] = $param;
        }

        try {
            $this->_pdo->beginTransaction();
            call_user_func_array($callback, $args);
            $this->_pdo->commit();
        } catch (\PDOException $e) {
            $this->_pdo->rollBack();
            return false;
        }

        return true;
    }
}