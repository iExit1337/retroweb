<?php

namespace App\Model\User;

use App\Model\Messages\Messages\Message;
use App\Model\Messages\Messages\MessagesFactory;
use App\Model\Messages\MessagesTopicsFactory;
use App\Model\Messages\MessageTopic;
use App\Model\Messages\Subscribers\ListEntry;
use App\Model\Messages\Subscribers\ListFactory;
use App\Model\User\AccountSettings\AccountSettings;
use App\Model\User\AccountSettings\AccountSettingsFactory;
use App\Model\User\Ban\Ban;
use App\Model\User\Ban\BanFactory;
use App\Model\User\Blocks\Block;
use App\Model\User\Blocks\BlocksFactory;
use App\Model\User\Connections\Connection;
use App\Model\User\Connections\ConnectionsFactory;
use App\Model\User\Connections\ConnectionTypes;
use App\Model\User\Currencies\CurrenciesFactory;
use App\Model\User\Currencies\Currency;
use App\Model\User\Friends\Friendship;
use App\Model\User\Friends\FriendshipFactory;
use App\Model\User\ProfileSettings\ProfileSettings;
use App\Model\User\ProfileSettings\ProfileSettingsFactory;
use System\Config;
use System\App\Model\AbstractFactoryChildModel;

class User extends AbstractFactoryChildModel
{

    public function getRankAsString(): string
    {

        $rank = $this->getInt("rank");
        do {
            $rankText = $this->getConfig()->get("rank_names", $rank--);
        } while ($rankText == null);

        return $rankText;
    }

    public function receiveDailyBonus(): bool
    {
        return $this->getConnection()->makeTransaction(function (\PDO $pdo, User $user, Config $config) {

            $user->set("last_daily_timestamp", time());
            $user->set("credits", ($user->getInt("credits")) + ($config->getInt("daily_bonus", "credits")));
            $user->set("points", ($user->getInt("points")) + ($config->getInt("daily_bonus", "points")));
            $user->getPixelsCurrency()
                ->set("amount", $user->getPixels() + ($config->getInt("daily_bonus", "pixels")));
        }, [
            $this,
            $this->getConfig()
        ]);
    }

    public function createProfileSettings(): void
    {

        $profileSettingsFactory = $this->getFactoryManager()->get(ProfileSettingsFactory::class);
        $profileSettingsFactory->createObject([
            'user_id' => $this->getInt("id")
        ]);
    }

    /**
     * @return Block[]
     */
    public function getBlockedUsers(): array
    {
        /**
         * @var $blocksFactory BlocksFactory
         */
        $blocksFactory = $this->getFactoryManager()->get(BlocksFactory::class);

        return $blocksFactory->getByUser($this);
    }

    public function hasBlocked(User $user): bool
    {
        /**
         * @var $blocksFactory BlocksFactory
         */
        $blocksFactory = $this->getFactoryManager()->get(BlocksFactory::class);

        return $blocksFactory->getBlockByUsers($this, $user) != null;
    }

    public function createAccountSettings(): void
    {
        $accountSettingsFactory = $this->getFactoryManager()->get(AccountSettingsFactory::class);
        $accountSettingsFactory->createObject([
            'user_id' => $this->getInt("id"),
            'credits' => $this->getInt("credits")
        ]);
    }

    public function getCreditsAsString(): string
    {

        return number_format($this->getInt("credits"), 0, ",", ".");
    }

    public function getPixelsAsString(): string
    {

        return number_format($this->getPixels(), 0, ",", ".");
    }

    public function getPointsAsString(): string
    {

        return number_format($this->getInt("points"), 0, ",", ".");
    }

    public function getLatestBan(): ?Ban
    {
        /**
         * @var $banFactory BanFactory
         */
        $banFactory = $this->getFactoryManager()->get(BanFactory::class);

        return $banFactory->getLatestByUser($this);
    }

    public function canDeleteComments(): bool
    {

        return $this->getInt("rank") >= $this->getConfig()->getInt("news_comments", "delete_min_rank");
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getMessagesTopics(): array
    {

        $messages = [];
        /**
         * @var $messagesTopicsFactory MessagesTopicsFactory
         */
        $messagesTopicsFactory = $this->getFactoryManager()->get(MessagesTopicsFactory::class);
        /**
         * @var $messagesSubscribersListsFactory ListFactory
         */
        $messagesSubscribersListsFactory = $this->getFactoryManager()->get(ListFactory::class);
        /**
         * @var $listEntries ListEntry[]
         */
        $listEntries = $messagesSubscribersListsFactory->getListEntriesByUser($this);

        foreach ($listEntries as $entry) {
            $messages[] = $messagesTopicsFactory->getByColumn('subscribers_list_id', $entry->getInt("list_id"));
        }

        usort($messages, function (MessageTopic $messageTopic1, MessageTopic $messageTopic2) {

            $latestMessage1Timestamp = $messageTopic1->getLatestMessage()->getInt("timestamp");
            $latestMessage2Timestamp = $messageTopic2->getLatestMessage()->getInt("timestamp");

            return $latestMessage2Timestamp <=> $latestMessage1Timestamp;
        });

        return $messages;
    }

    public function hasUnreadMessages(): bool
    {

        $query = $this->getConnection()
            ->getPDO()
            ->prepare('SELECT COUNT(`id`) As `has_seen_count` FROM `cms_messages_seen` WHERE `user_id` = :user_id AND `has_seen` = :has_seen');
        $query->execute([
            ':has_seen' => 0,
            ':user_id' => $this->getInt("id")
        ]);

        return $query->fetchObject()->has_seen_count > 0;
    }

    /**
     * @return Message[]
     */
    public function getUnreadMessages(): array
    {

        $query = $this->getConnection()
            ->getPDO()
            ->prepare('SELECT `id`, `message_id` FROM `cms_messages_seen` WHERE `user_id` = :user_id AND `has_seen` = :has_seen');
        $query->execute([
            ':has_seen' => 0,
            ':user_id' => $this->getInt("id")
        ]);

        $messages = [];
        if ($query->rowCount() > 0) {
            $messagesFactory = $this->getFactoryManager()->get(MessagesFactory::class);
            while ($row = $query->fetchObject()) {
                $messages[] = $messagesFactory->getById($row->message_id);
            }
        }

        return $messages;
    }

    /**
     * @return AccountSettings
     */
    public function getAccountSettings(): AccountSettings
    {
        /**
         * @var $accountSettingsFactory AccountSettingsFactory
         */
        $accountSettingsFactory = $this->getFactoryManager()->get(AccountSettingsFactory::class);

        return $accountSettingsFactory->getByUser($this);
    }

    /**
     * @return ProfileSettings
     */
    public function getProfileSettings(): ProfileSettings
    {
        /**
         * @var $profileSettingsFactory ProfileSettingsFactory
         */
        $profileSettingsFactory = $this->getFactoryManager()->get(ProfileSettingsFactory::class);

        return $profileSettingsFactory->getByUser($this);
    }

    /**
     * @return Currency
     */
    public function getPixelsCurrency(): Currency
    {
        /**
         * @var $currenciesFactory CurrenciesFactory
         */
        $currenciesFactory = $this->getFactoryManager()->get(CurrenciesFactory::class);

        return $currenciesFactory->getByUser(0, $this);
    }

    /**
     * @return int
     */
    public function getPixels(): int
    {

        return $this->getPixelsCurrency()->getInt("amount");
    }

    /**
     * @return Friendship[]
     * @throws \Exception
     */
    public function getFriendships(): array
    {
        /**
         * @var $friendsFactory FriendshipFactory
         */
        $friendsFactory = $this->getFactoryManager()->get(FriendshipFactory::class);

        return $friendsFactory->getByUser($this);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function hasFriendship(User $user): bool
    {
        /**
         * @var $friendsFactory FriendshipFactory
         */
        $friendsFactory = $this->getFactoryManager()->get(FriendshipFactory::class);

        return $friendsFactory->getByUsers($this, $user) != null;
    }

    /**
     * @return Connection|null
     */
    public function getSteamConnection(): ?Connection
    {
        /**
         * @var $connectionsFactory ConnectionsFactory
         */
        $connectionsFactory = $this->getFactoryManager()->get(ConnectionsFactory::class);

        return $connectionsFactory->getByUser(ConnectionTypes::STEAM, $this);
    }

    /**
     * @return string
     */
    public function getSSOTicket(): string
    {
        $sso_ticket = "";
        $length = 6;
        $name_pos = rand(0, $length - 1);
        for ($i = 0; $i < $length; $i++) {
            if ($i == $name_pos) {
                $sso_ticket .= $this->get("username") . "-";
            } else {
                $sso_ticket .= rand(10000, 999999) . "-";
            }
        }

        $sso_ticket = substr($sso_ticket, 0, -1);

        $this->set("auth_ticket", $sso_ticket);

        return $sso_ticket;
    }
}