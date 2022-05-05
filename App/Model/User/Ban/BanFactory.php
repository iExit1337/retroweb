<?php

namespace App\Model\User\Ban;


use App\Model\User\User;
use System\App\Model\AbstractFactoryModel;

class BanFactory extends AbstractFactoryModel
{
    protected function getTable(): string
    {
        return 'bans';
    }

    protected function hasChildren(): bool
    {
        return true;
    }

    protected function getChildrenInstance(): string
    {
        return Ban::class;
    }

    public function getLatestByUser(User $user): ?Ban
    {
        $query = $this->getConnection()->getPDO()->prepare("SELECT `id` FROM `{$this->getTable()}` WHERE `user_id` = :user_id ORDER BY id DESC");
        $query->execute([":user_id" => $user->getInt('id')]);

        if ($query->rowCount() > 0) {
            $row = $query->fetchObject();
            /**
             * @var $ban Ban
             */
            $ban = $this->getById($row->id);

            return $ban;
        }

        return null;
    }
}