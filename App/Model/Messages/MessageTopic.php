<?php

namespace App\Model\Messages;

use App\Model\Messages\Messages\Message;
use App\Model\Messages\Messages\MessagesFactory;
use App\Model\Messages\Subscribers\ListEntry;
use App\Model\Messages\Subscribers\ListFactory;
use App\Model\User\User;
use System\App\Model\AbstractFactoryChildModel;

class MessageTopic extends AbstractFactoryChildModel
{

    /**
     * @return ListEntry[]
     * @throws \Exception
     */
    public function getSubscriberEntries(): array
    {
        /**
         * @var $listFactory ListFactory
         */
        $listFactory = $this->getFactoryManager()->get(ListFactory::class);
        $entries = $listFactory->getByMessageTopic($this);

        return $entries;
    }

	/**
	 * @return Message|null
	 */
    public function getLatestMessage(): ?Message
    {
        /**
         * @var $messagesFactory MessagesFactory
         */
        $messagesFactory = $this->getFactoryManager()->get(MessagesFactory::class);
        return $messagesFactory->getLatestByTopic($this);
    }

    /**
     * @return Message[]
     * @throws \Exception
     */
    public function getMessages(): array
    {
        /**
         * @var $messagesFactory MessagesFactory
         */
        $messagesFactory = $this->getFactoryManager()->get(MessagesFactory::class);
        return $messagesFactory->getByTopic($this);
    }

	/**
	 * @param User $user
	 *
	 * @return bool
	 */
    public function hasUnreadMessages(User $user): bool
    {
        if ($user->hasUnreadMessages()) {
            /**
             * @var $unreadMessages Message[]
             */
            $unreadMessages = $user->getUnreadMessages();
            foreach ($unreadMessages as $message) {
                if ($message->getInt("message_topic_id") == $this->getInt("id")) {
                    return true;
                }
            }
        }

        return false;
    }

	/**
	 * @param User $user
	 */
    public function setUnreadMessagesAsRead(User $user): void
    {
        if ($user->hasUnreadMessages()) {
            $unreadMessages = $user->getUnreadMessages();

            foreach ($unreadMessages as $message) {
                if ($message->getInt("message_topic_id") == $this->getInt("id")) {
                    $message->setAsSeen($user);
                }
            }
        }
    }

    /**
     * @param User $user
     * @throws \Exception
     */
    public function leave(User $user): void
    {
        foreach ($this->getSubscriberEntries() as $subscriberEntry) {
            if ($user->getInt("id") == $subscriberEntry->getUser()->getInt("id")) {
                $subscriberEntry->delete();
            }
        }
    }

    /**
     * @param User $user
     * @return bool
     * @throws \Exception
     */
    public function isSubscriber(User $user): bool
    {
        foreach ($this->getSubscriberEntries() as $entry) {
            if ($entry->getUser()->getInt("id") == $user->getInt("id")) {
                return true;
            }
        }

        return false;
    }
}