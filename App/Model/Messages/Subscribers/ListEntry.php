<?php


namespace App\Model\Messages\Subscribers;


use App\Model\User\User;
use App\Model\User\UserFactory;
use System\App\Model\AbstractFactoryChildModel;

class ListEntry extends AbstractFactoryChildModel
{

	/**
	 * @return User
	 */
    public function getUser(): User
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