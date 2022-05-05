<?php

namespace App\Model\User\Currencies;

use App\Model\User\User;
use System\App\Model\AbstractFactoryModel;

class CurrenciesFactory extends AbstractFactoryModel
{

    protected function getTable(): string
    {

        return "users_currency";
    }

    protected function hasChildren(): bool
    {

        return true;
    }

    protected function getChildrenInstance(): string
    {

        return Currency::class;
    }

    public function getNewId(): int
    {

        $query = $this->getConnection()
            ->getPDO()
            ->prepare('SELECT `id` FROM `' . $this->getTable() . '` ORDER BY `id` DESC');
        $query->execute();
        if ($query->rowCount() > 0) {
            return $query->fetchObject()->id + 1;
        } else {
            return 1;
        }
    }

    /**
     * @param $type
     * @param User $user
     * @return Currency|null
     * @throws \Exception
     */
    public function getByUser($type, User $user): ?Currency
    {

        $query = $this->getConnection()
            ->getPDO()
            ->prepare('SELECT `id` FROM `' . $this->getTable() . '` WHERE `user_id` = :uid AND `type` = :t');
        $query->execute([
            ':uid' => $user->getInt("id"),
            ':t' => $type
        ]);

        if ($query->rowCount() > 0) {
            /**
             * @var $currency Currency
             */
            $currency = $this->getByRow($query->fetchObject());

            return $currency;
        } else {
            $this->createObject([
                'type' => (int)$type,
                'user_id' => $user->getInt("id"),
                'amount' => 0
            ]);

            return $this->getByUser($type, $user);
        }

    }
}