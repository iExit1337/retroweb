<?php

namespace App\Model\User\AccountSettings;

use App\Model\User\User;
use System\App\Model\AbstractFactoryModel;

class AccountSettingsFactory extends AbstractFactoryModel
{

    protected function getTable(): string
    {

        return "users_settings";
    }

    protected function hasChildren(): bool
    {

        return true;
    }

    protected function getChildrenInstance(): string
    {

        return AccountSettings::class;
    }

    public function getByUser(User $user): ?AccountSettings
    {

        /**
         * @var $accountSettings AccountSettings|null
         */
        $accountSettings = $this->getByColumn("user_id", $user->getInt("id"));

        return $accountSettings;
    }
}