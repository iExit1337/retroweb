<?php


namespace App\Model\User;

use App\Model\User\Connections\ConnectionsFactory;
use System\App\Model\AbstractFactoryModel;

class UserFactory extends AbstractFactoryModel
{

    protected function getTable(): string
    {
        return "users";
    }

    protected function hasChildren(): bool
    {
        return true;
    }

    protected function getChildrenInstance(): string
    {
        return User::class;
    }

    /**
     * @return User[]
     * @throws \Exception
     */
    public function getStaffs(): array
    {
        $query = $this->getConnection()->getPDO()->prepare('SELECT `id` FROM `' . $this->getTable() . '` WHERE `rank` > :min_rank');
        $query->execute([':min_rank' => (int)$this->getConfig()->get("staff_page", "min_rank")]);

        $staffs = [];

        if ($query->rowCount() > 0) {
            while ($row = $query->fetchObject()) {
                $staffs[] = $this->getByRow($row);
            }
        }

        return $staffs;
    }


    /**
     * @param string $term
     * @return User[]
     * @throws \Exception
     */
    public function getWhereNameLike(string $term): array
    {
        $query = $this->getConnection()->getPDO()->prepare('SELECT `id` FROM `' . $this->getTable() . '` WHERE `username` LIKE :username');
        $query->execute([':username' => '%' . $term . '%']);

        $users = [];

        if ($query->rowCount() > 0) {
            while ($row = $query->fetchObject()) {
                $users[] = $this->getByRow($row);
            }
        }

        return $users;

    }

    /**
     * @param int $connectionType
     * @param array $args
     * @return User|null
     */
    public function getByConnection(int $connectionType, array $args): ?User
    {
        /**
         * @var $connectionsFactory ConnectionsFactory
         */
        $connectionsFactory = $this->getFactoryManager()->get(ConnectionsFactory::class);
        $connection = $connectionsFactory->getByTypeAndArgs($connectionType, $args);

        if($connection != null) {
            return $connection->getUser();
        }

        return null;
    }
}