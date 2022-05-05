<?php


namespace App\Model\News\Comments;


use App\Model\News\News;
use System\App\Model\AbstractFactoryModel;

class CommentsFactory extends AbstractFactoryModel
{

    protected function getTable(): string
    {
        return "cms_news_comments";
    }

    protected function hasChildren(): bool
    {
        return true;
    }

    protected function getChildrenInstance(): string
    {
        return Comment::class;
    }

    /**
     * @param News $news
     * @return Comment|null
     * @throws \Exception
     */
    public function getLatestCommentByNews(News $news): ?Comment
    {
        $query = $this->getConnection()->getPDO()->prepare('SELECT `id` FROM `' . $this->getTable() . '` WHERE `news_id` = :news_id ORDER BY `id` DESC LIMIT 1');
        $query->execute([':news_id' => $news->getInt("id")]);

        if ($query->rowCount() > 0) {
            $row = $query->fetchObject();
            /**
             * @var $comment Comment
             */
            $comment = $this->getByRow($row);
            return $comment;
        }

        return null;
    }

    /**
     * @param News $news
     * @return Comment[]
     * @throws \Exception
     */
    public function getAllByNews(News $news): array
    {
        $query = $this->getConnection()->getPDO()->prepare('SELECT `id` FROM `' . $this->getTable() . '` WHERE `news_id` = :news_id ORDER BY `id` DESC');
        $query->execute([':news_id' => $news->getInt("id")]);

        $data = [];

        if ($query->rowCount() > 0) {
            while ($row = $query->fetchObject()) {
                $data[] = $this->getByRow($row);
            }
        }

        return $data;
    }
}