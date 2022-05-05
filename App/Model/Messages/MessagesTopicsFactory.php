<?php


namespace App\Model\Messages;


use App\Model\Messages\Messages\MessagesFactory;
use App\Model\Messages\Subscribers\ListFactory;
use App\Model\User\User;
use System\App\Model\AbstractFactoryModel;

class MessagesTopicsFactory extends AbstractFactoryModel
{

    protected function getTable(): string
    {
        return "cms_messages_topics";
    }

    protected function hasChildren(): bool
    {
        return true;
    }

    protected function getChildrenInstance(): string
    {
        return MessageTopic::class;
    }

    /**
     * @param User $user
     * @return MessageTopic[]
     * @throws \Exception
     */
    public function getByUser(User $user): array
    {
        $query = $this->getConnection()->getPDO()->prepare('SELECT `id` FROM `' . $this->getTable() . '` WHERE `user_id` = :user_id ORDER BY `id` DESC');
        $query->execute([':user_id' => $user->getInt("id")]);

        $data = [];
        if ($query->rowCount() > 0) {
            while ($row = $query->fetchObject()) {
                $data[] = $this->getByRow($row);
            }
        }
        return $data;
    }

	/**
	 * @param string $subject
	 * @param string $message
	 * @param array  $receivers
	 * @param User   $creator
	 */
    public function createNewTopic(string $subject, string $message, array $receivers, User $creator): void
    {
        $this->getConnection()->makeTransaction(function (\PDO $pdo, MessagesTopicsFactory $messagesTopicsFactory, User $creator, array $receivers, $subject, $message, ListFactory $listFactory, MessagesFactory $messagesFactory) {
            $lastListEntry = $listFactory->getLastListEntry();
            $id = $lastListEntry != null ? $lastListEntry->getInt("id") + 1 : 1;

            $listFactory->createObject([
                'user_id' => $creator->getInt("id"),
                'list_id' => $id
            ]);

            foreach ($receivers as $receiver) {
                $listFactory->createObject([
                    'user_id' => $receiver->getInt("id"),
                    'list_id' => $id
                ]);
            }

            $topic = $messagesTopicsFactory->createObject([
                'subscribers_list_id' => $id,
                'subject' => $subject,
                'creator_id' => $creator->getInt("id")
            ]);

            if ($topic != null) {
                $messagesFactory->_createObject([
                    'user_id' => $creator->getInt("id"),
                    'message_topic_id' => $topic->getInt("id"),
                    'message' => $message,
                    'timestamp' => time()
                ], $creator);
            } else {
                throw new \PDOException("Error while creating Topic");
            }
        }, [
            $this, $creator, $receivers, $subject, $message, $this->getFactoryManager()->get(ListFactory::class), $this->getFactoryManager()->get(MessagesFactory::class)
        ]);
    }
}