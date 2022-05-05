<?php

namespace App\Model\User\Connections;

use App\Model\User\User;
use System\App\Model\AbstractFactoryModel;

class ConnectionsFactory extends AbstractFactoryModel
{

    protected function getTable(): string
    {

        return "cms_users_connections";
    }

    protected function hasChildren(): bool
    {

        return true;
    }

    protected function getChildrenInstance(): string
    {

        return Connection::class;
    }

    /**
     * @param int $type
     * @param User $user
     * @return Connection|null
     * @throws \Exception
     */
    public function getByUser(int $type, User $user): ?Connection
    {

        $query = $this->getConnection()
            ->getPDO()
            ->prepare('SELECT `id` FROM `' . $this->getTable() . '` WHERE `type` = :type AND `user_id` = :user_id');
        $query->execute([
            ':user_id' => $user->getInt("id"),
            ':type' => $type
        ]);

        if ($query->rowCount() > 0) {
            /**
             * @var $connection Connection
             */
            $connection = $this->getByRow($query->fetchObject());
            return $connection;
        } else {
            return null;
        }
    }

    /**
     * @param int $type
     * @param array $args
     * @return Connection|null
     * @throws \Exception
     */
    public function getByTypeAndArgs(int $type, array $args): ?Connection
    {
        $queryString = "SELECT `id` FROM `" . $this->getTable() . "` WHERE `type` = :type";

        $query = $this->getConnection()->getPDO()->prepare($queryString);
        $query->execute([':type' => $type]);

        while ($row = $query->fetchObject()) {
            /**
             * @var $connection Connection
             */
            $connection = $this->getByRow($row);
            $data = $connection->getAPIData();
            foreach ($args as $key => $value) {
                if ($data->{$key} != $value) {
                    continue;
                }

                return $connection;
            }
        }

        return null;
    }
}