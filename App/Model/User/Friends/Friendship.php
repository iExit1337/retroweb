<?php

namespace App\Model\User\Friends;

use App\Model\User\User;
use App\Model\User\UserFactory;
use System\App\Model\AbstractFactoryChildModel;

class Friendship extends AbstractFactoryChildModel
{

    public function getUserOne(): User
    {
        /**
         * @var $userFactory UserFactory
         */
        $userFactory = $this->getFactoryManager()->get(UserFactory::class);
        /**
         * @var $user User
         */
        $user = $userFactory->getById($this->getInt("user_one_id"));

        return $user;
    }

    public function getUserTwo(): User
    {
        /**
         * @var $userFactory UserFactory
         */
        $userFactory = $this->getFactoryManager()->get(UserFactory::class);
        /**
         * @var $user User
         */
        $user = $userFactory->getById($this->getInt("user_two_id"));

        return $user;
    }
}