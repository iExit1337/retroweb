<?php

namespace App\Model\User\Blocks;

use App\Model\User\User;
use System\App\Model\AbstractFactoryModel;

class BlocksFactory extends AbstractFactoryModel
{

    protected function getTable(): string
    {

        return "cms_users_blocks";
    }

    protected function hasChildren(): bool
    {

        return true;
    }

    protected function getChildrenInstance(): string
    {

        return Block::class;
    }

    /**
     * @param User $user
     * @return Block[]
     * @throws \Exception
     */
    public function getByUser(User $user): array
    {

        $query = $this->getConnection()
            ->getPDO()
            ->prepare('SELECT `id` FROM `' . $this->getTable() . '` WHERE `user_id` = :user_id');
        $query->execute([':user_id' => $user->getInt("id")]);

        $data = [];
        while ($row = $query->fetchObject()) {
            $data[] = $this->getByRow($row);
        }

        return $data;
    }

    /**
     * @param User $user
     * @param User $blockedUser
     * @return Block|null
     * @throws \Exception
     */
    public function getBlockByUsers(User $user, User $blockedUser): ?Block
    {

        $query = $this->getConnection()
            ->getPDO()
            ->prepare('SELECT `id` FROM `' . $this->getTable() . '` WHERE `user_id` = :user_id AND `blocked_id` = :blocked_id');
        $query->execute([
            ':user_id' => $user->getInt("id"),
            ':blocked_id' => $blockedUser->getInt("id")
        ]);

        if ($query->rowCount() > 0) {
            /**
             * @var $block Block
             */
            $block = $this->getByRow($query->fetchObject());

            return $block;
        } else {
            return null;
        }
    }
}