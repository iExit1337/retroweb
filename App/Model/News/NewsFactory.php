<?php


namespace App\Model\News;


use System\App\Model\AbstractFactoryModel;

class NewsFactory extends AbstractFactoryModel
{

    protected function getTable(): string
    {
        return "cms_news";
    }

    protected function hasChildren(): bool
    {
        return true;
    }

    protected function getChildrenInstance(): string
    {
        return News::class;
    }

    /**
     * @param int $limit
     *
     * @return News[]
     * @throws \Exception
     */
    public function getLatestByLimit(int $limit): array
    {
        $query = $this->getConnection()->getPDO()->prepare('SELECT `id` FROM `' . $this->getTable() . '` ORDER BY `id` DESC LIMIT ' . $limit);
        $query->execute();

        $data = [];

        if ($query->rowCount() > 0) {
            while ($row = $query->fetchObject()) {
                $data[] = $this->getByRow($row);
            }
        }

        return $data;
    }
}