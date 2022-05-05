<?php


namespace App\Model\Messages\Messages;


use App\Model\Messages\MessagesTopicsFactory;
use App\Model\Messages\MessageTopic;
use App\Model\Messages\Subscribers\ListEntry;
use App\Model\Messages\Subscribers\ListFactory;
use App\Model\User\User;
use System\App\Model\AbstractFactoryModel;

class MessagesFactory extends AbstractFactoryModel
{

    protected function getTable(): string
    {
        return "cms_messages";
    }

    protected function hasChildren(): bool
    {
        return true;
    }

    protected function getChildrenInstance(): string
    {
        return Message::class;
    }

    /**
     * @param MessageTopic $topic
     * @return Message[]
     * @throws \Exception
     */
    public function getByTopic(MessageTopic $topic): array
    {
        $query = $this->getConnection()->getPDO()->prepare('SELECT `id` FROM `' . $this->getTable() . '` WHERE `message_topic_id` = :topic_id ORDER BY `timestamp`');
        $query->execute([':topic_id' => $topic->getInt("id")]);

        $messages = [];
        if ($query->rowCount() > 0) {
            while ($row = $query->fetchObject()) {
                $messages[] = $this->getByRow($row);
            }
        }

        return $messages;
    }

    /**
     * @param array $data
     * @param User $creator
     * @return null|Message
     * @throws \Exception
     */
    public function _createObject(array $data, User $creator): ?Message
    {
        /**
         * @var $message Message|null
         */
        $message = parent::createObject($data);
        if ($message == null) return null;

        $topicId = $data['message_topic_id'];
        /**
         * @var $topicFactory MessagesTopicsFactory
         */
        $topicFactory = $this->getFactoryManager()->get(MessagesTopicsFactory::class);
        /**
         * @var $topic MessageTopic|null
         */
        $topic = $topicFactory->getById($topicId);
        if ($topic != null) {
            /**
             * @var $entries ListEntry[]
             */
            $entries = $topic->getSubscriberEntries();
            foreach ($entries as $entry) {
                /**
                 * @var $user User
                 */
                $user = $entry->getUser();
                if ($user->getInt("id") != $creator->getInt("id")) {
                    try {
                        $query = $this->getConnection()->getPDO()->prepare('INSERT INTO `cms_messages_seen` SET `message_id` = :message_id, `user_id` = :user_id, `has_seen` = :has_seen');
                        $query->execute([':message_id' => $message->getInt("id"), ':user_id' => $entry->getUser()->getInt("id"), ':has_seen' => 0]);
                    } catch (\PDOException $e) {
                        exit($e->getMessage());
                    }
                }
            }
        }

        return $message;
    }

    /**
     * @param MessageTopic $messageTopic
     * @return null|Message
     * @throws \Exception
     */
    public function getLatestByTopic(MessageTopic $messageTopic): ?Message
    {
        $query = $this->getConnection()->getPDO()->prepare('SELECT `id` FROM `' . $this->getTable() . '` WHERE `message_topic_id` = :topic_id ORDER BY id DESC LIMIT 1');
        $query->execute([':topic_id' => $messageTopic->getInt("id")]);

        /**
         * @var $message Message|null
         */
        $message = null;

        if ($query->rowCount() > 0) {
            $message = $this->getByRow($query->fetchObject());
        }


        return $message;
    }
}