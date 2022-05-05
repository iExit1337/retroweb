<?php


namespace App\Model\Messages\Messages;


use App\Model\User\User;
use App\Model\User\UserFactory;
use System\App\Model\AbstractFactoryChildModel;

class Message extends AbstractFactoryChildModel
{
    public function getUser(): ?User
    {
        /**
         * @var $userFactory UserFactory
         */
        $userFactory = $this->getFactoryManager()->get(UserFactory::class);
        /**
         * @var $user User|null
         */
        $user = $userFactory->getById($this->getInt("user_id"));

        return $user;
    }

    public function setAsSeen(User $user): void
    {
        $query = $this->getConnection()->getPDO()->prepare('UPDATE `cms_messages_seen` SET `has_seen` = :has_seen, `seen_timestamp` = :seen_timestamp WHERE `message_id` = :message_id AND `user_id` = :user_id');
        $query->execute([':has_seen' => 1, ':seen_timestamp' => time(), ':message_id' => $this->getInt("id"), ':user_id' => $user->getInt("id")]);
    }
}