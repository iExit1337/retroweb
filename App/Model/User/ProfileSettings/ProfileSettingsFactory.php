<?php

namespace App\Model\User\ProfileSettings;

use App\Model\User\User;
use System\App\Model\AbstractFactoryModel;

class ProfileSettingsFactory extends AbstractFactoryModel
{

    protected function getTable(): string
    {
        return "cms_profile_settings";
    }

    protected function hasChildren(): bool
    {
        return true;
    }

    protected function getChildrenInstance(): string
    {
        return ProfileSettings::class;
    }

    public function getByUser(User $user): ?ProfileSettings
    {
        /**
         * @var $profileSettings ProfileSettings|null
         */
        $profileSettings = $this->getByColumn("user_id", (int)$user->getInt("id"));
        return $profileSettings;
    }
}