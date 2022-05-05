<?php


namespace App\Model\Campaign;


use System\App\Model\AbstractFactoryModel;

class CampaignFactory extends AbstractFactoryModel
{

    protected function getTable(): string
    {
        return "cms_campaigns";
    }

    protected function hasChildren(): bool
    {
        return true;
    }

    protected function getChildrenInstance(): string
    {
        return Campaign::class;
    }

    /**
     * @return Campaign[]
     */
    public function getCampaigns(): array
    {
        $query = $this->getConnection()->getPDO()->prepare("SELECT `id` FROM `cms_campaigns` ORDER BY `id` DESC");
        $query->execute();

        $data = [];

        if ($query->rowCount() > 0) {
            while($row = $query->fetchObject()){
                $data[] = $this->getById($row->id);
            }
        }

        return $data;
    }
}