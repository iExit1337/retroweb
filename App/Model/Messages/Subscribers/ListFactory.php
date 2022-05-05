<?php


namespace App\Model\Messages\Subscribers;


use App\Model\Messages\MessageTopic;
use App\Model\User\User;
use System\App\Model\AbstractFactoryModel;

class ListFactory extends AbstractFactoryModel
{

    protected function getTable(): string
    {
        return "cms_messages_subscribers_lists";
    }

    protected function hasChildren(): bool
    {
        return true;
    }

    protected function getChildrenInstance(): string
    {
        return ListEntry::class;
    }

    /**
     * @param User $user
     * @return ListEntry[]
     * @throws \Exception
     */
    public function getListEntriesByUser(User $user): array
    {
        $query = $this->getConnection()->getPDO()->prepare('SELECT `id` FROM `' . $this->getTable() . '` WHERE `user_id` = :user_id');
        $query->execute([':user_id' => $user->getInt("id")]);

        $entries = [];

        if ($query->rowCount() > 0) {
            while ($row = $query->fetchObject()) {
                $entries[] = $this->getByRow($row);
            }
        }

        return $entries;
    }

    /**
     * @param MessageTopic $messageTopic
     * @return ListEntry[]
     * @throws \Exception
     */
    public function getByMessageTopic(MessageTopic $messageTopic): array
    {
        $query = $this->getConnection()->getPDO()->prepare('SELECT `id` FROM `' . $this->getTable() . '` WHERE `list_id` = :list_id');
        $query->execute([':list_id' => $messageTopic->getInt("subscribers_list_id")]);

        $entries = [];

        if ($query->rowCount() > 0) {
            while ($row = $query->fetchObject()) {
                $entries[] = $this->getByRow($row);
            }
        }

        return $entries;
    }

    /**
     * @param int $id
     * @return ListEntry[]
     * @throws \Exception
     */
    public function getEntriesByListId(int $id): array
    {
        $query = $this->getConnection()->getPDO()->prepare('SELECT `id` FROM `' . $this->getTable() . '` WHERE `list_id` = :list_id');
        $query->execute([':list_id' => $id]);

        $entries = [];
        if ($query->rowCount() > 0) {
            while ($row = $query->fetchObject()) {
                $entries[] = $this->getByRow($row);
            }
        }

        return $entries;
    }

    /**
     * @return ListEntry|null
     * @throws \Exception
     */
    public function getLastListEntry(): ?ListEntry
    {
        $query = $this->getConnection()->getPDO()->prepare('SELECT `id` FROM `' . $this->getTable() . '` ORDER BY `id` DESC LIMIT 1');
        $query->execute([]);

        if ($query->rowCount() > 0) {
            $row = $query->fetchObject();
            /**
             * @var $listEntry ListEntry|null
             */
            $listEntry = $this->getByRow($row);
            return $listEntry;
        }

        return null;
    }
}