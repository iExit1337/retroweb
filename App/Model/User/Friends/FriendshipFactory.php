<?php

namespace App\Model\User\Friends;

use App\Model\User\User;
use System\App\Model\AbstractFactoryModel;

class FriendshipFactory extends AbstractFactoryModel
{

    protected function getTable(): string
    {

        return "messenger_friendships";
    }

    protected function hasChildren(): bool
    {

        return true;
    }

    protected function getChildrenInstance(): string
    {

        return Friendship::class;
    }

    /**
     * @param User $user
     * @return Friendship[]
     * @throws \Exception
     */
    public function getByUser(User $user): array
    {

        $query = $this->getConnection()
            ->getPDO()
            ->prepare('SELECT `id`, `user_one_id`, `user_two_id` FROM `' . $this->getTable() . '` WHERE `user_one_id` = :user_id OR `user_two_id` = :user_id');
        $query->execute([':user_id' => $user->getInt("id")]);

        $data = [];
        $addedUserIds = [];

        while ($row = $query->fetchObject()) {
            $friendUserId = $row->user_one_id == $user->getInt("id") ? $row->user_two_id : $row->user_one_id;
            if (!isset($addedUserIds[$friendUserId])) {
                $addedUserIds[$friendUserId] = true;
                $data[] = $this->getByRow($row);
            }
        }

        return $data;
    }

    /**
     * @param User $userOne
     * @param User $userTwo
     * @return Friendship|null
     * @throws \Exception
     */
    public function getByUsers(User $userOne, User $userTwo): ?Friendship
    {

        $query = $this->getConnection()
            ->getPDO()
            ->prepare('SELECT `id` FROM `' . $this->getTable() . '` WHERE `user_one_id` = :uoi AND `user_two_id` = :uti OR `user_one_id` = :uti AND `user_two_id` = :uoi');
        $query->execute([
            ':uoi' => $userOne->getInt("id"),
            ':uti' => $userTwo->getInt("id")
        ]);

        if ($query->rowCount() > 0) {
            /**
             * @var $friendship Friendship
             */
            $friendship = $this->getByRow($query->fetchObject());
            return $friendship;
        } else {
            return null;
        }
    }
}