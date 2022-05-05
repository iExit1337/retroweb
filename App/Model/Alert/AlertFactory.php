<?php

namespace App\Model\Alert;


use System\App\Model\AbstractFactoryModel;

class AlertFactory extends AbstractFactoryModel
{

    protected function getTable(): string
    {
        return 'cms_alerts';
    }

    protected function hasChildren(): bool
    {
        return true;
    }

    protected function getChildrenInstance(): string
    {
        return Alert::class;
    }

    /**
     * @param int $active
     * @return array
     * @throws \Exception
     */
    private function getByActive(int $active): array
    {
        $query = $this->getConnection()->getPDO()->prepare('SELECT `id` FROM `' . $this->getTable() . '` WHERE `active` = :active');
        $query->execute([':active' => $active]);

        $data = [];
        if ($query->rowCount() > 0) {
            while ($row = $query->fetchObject()) {
                $data[] = $this->getByRow($row);
            }
        }
        return $data;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getActiveAlerts(): array
    {
        return $this->getByActive(1);
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getInactiveAlerts(): array
    {
        return $this->getByActive(0);
    }

    /**
     * @return Alert|null
     */
    public function getRandomAlert(): ?Alert
    {
        $query = $this->getConnection()->getPDO()->prepare("SELECT `id` FROM `{$this->getTable()}` WHERE `active` = '1' ORDER BY RAND() LIMIT 1");
        $query->execute();

        if ($query->rowCount() > 0) {
            $row = $query->fetchObject();
            /**
             * @var $alert Alert|null
             */
            $alert = $this->getById($row->id);
            return $alert;
        }

        return null;
    }

}