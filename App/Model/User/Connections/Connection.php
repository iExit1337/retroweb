<?php

namespace App\Model\User\Connections;

use App\Model\User\User;
use App\Model\User\UserFactory;
use System\App\Model\AbstractFactoryChildModel;

class Connection extends AbstractFactoryChildModel
{

    public function getAPIData(): \stdClass
    {
        return json_decode($this->get("api_data"));
    }

    public function getUser(): ?User
    {
        /**
         * @var $userFactory UserFactory
         */
        $userFactory = $this->getFactoryManager()->get(UserFactory::class);
        /**
         * @var $user User
         */
        $user = $userFactory->getById($this->getInt("user_id"));
        return $user;
    }

}