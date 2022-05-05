<?php


namespace App\Model\News\Voting;


use App\Model\News\News;
use App\Model\User\User;
use System\App\Model\AbstractFactoryModel;

class VotingFactory extends AbstractFactoryModel
{

    protected function getTable(): string
    {
        return "cms_news_voting";
    }

    protected function hasChildren(): bool
    {
        return true;
    }

    protected function getChildrenInstance(): string
    {
        return Voting::class;
    }

    /**
     * @param News $news
     * @return Voting[]
     * @throws \Exception
     */
    public function getByNews(News $news): array
    {
        $query = $this->getConnection()->getPDO()->prepare('SELECT `id` FROM `' . $this->getTable() . '` WHERE `news_id` = :news_id');
        $query->execute([':news_id' => $news->getInt("id")]);

        $data = [];

        if ($query->rowCount() > 0) {
            while ($row = $query->fetchObject()) {
                $data[] = $this->getByRow($row);
            }
        }

        return $data;
    }

	/**
	 * @param News $news
	 *
	 * @return int
	 */
    public function getDislikeCountByNews(News $news): int
    {
        $query = $this->getConnection()->getPDO()->prepare('SELECT COUNT(`id`) as `dislikes` FROM `' . $this->getTable() . '` WHERE `type` = :type AND `news_id` = :news_id');
        $query->execute([':news_id' => $news->getInt("id"), ':type' => 0]);

        $result = $query->fetchObject();

        return (int)$result->dislikes;
    }

	/**
	 * @param News $news
	 *
	 * @return int
	 */
    public function getLikeCountByNews(News $news): int
    {
        $query = $this->getConnection()->getPDO()->prepare('SELECT COUNT(`id`) as `likes` FROM `' . $this->getTable() . '` WHERE `type` = :type AND `news_id` = :news_id');
        $query->execute([':news_id' => $news->getInt("id"), ':type' => 1]);

        $result = $query->fetchObject();

        return (int)$result->likes;
    }

    /**
     * @param News $news
     * @param User $user
     * @return Voting|null
     * @throws \Exception
     */
    public function getByNewsAndUser(News $news, User $user): ?Voting
    {
        $query = $this->getConnection()->getPDO()->prepare('SELECT `id` FROM `' . $this->getTable() . '` WHERE `news_id` = :news_id AND `user_id` = :user_id');
        $query->execute([':news_id' => $news->getInt("id"), ':user_id' => $user->getInt("id")]);

        if ($query->rowCount() > 0) {
            $row = $query->fetchObject();
            /**
             * @var $voting Voting
             */
            $voting = $this->getByRow($row);
            return $voting;
        }

        return null;
    }
}