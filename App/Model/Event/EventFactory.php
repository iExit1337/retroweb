<?php


namespace App\Model\Event;


use System\App\Model\AbstractFactoryModel;

class EventFactory extends AbstractFactoryModel
{

    protected function getTable(): string
    {
        return 'cms_events';
    }

    protected function hasChildren(): bool
    {
        return true;
    }

    protected function getChildrenInstance(): string
    {
        return Event::class;
    }

    /**
     * @return Event[]
     */
    public function getActiveEvents(): array
    {

        $query = $this->getConnection()->getPDO()->prepare('SELECT `id` FROM `' . $this->getTable() . '` WHERE `start_time` < :current_time && `end_time` > :current_time');
        $query->execute([
            ":current_time" => time()
        ]);

        $data = [];

        if ($query->rowCount() > 0) {
            while ($row = $query->fetchObject()) {
                $data[] = $this->getById($row->id);
            }
        }

        return $data;
    }

    /**
     * @return Event[]
     */
    public function getUpcomingEvents(): array
    {
        $query = $this->getConnection()->getPDO()->prepare('SELECT `id` FROM `' . $this->getTable() . '` WHERE `start_time` > :time');
        $query->execute([
            ":time" => time()
        ]);

        $data = [];

        if ($query->rowCount() > 0) {
            while ($row = $query->fetchObject()) {
                $data[] = $this->getById($row->id);
            }
        }

        return $data;
    }

}