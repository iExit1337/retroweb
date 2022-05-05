<?php


namespace App\Model\News\Comments\Voting;


use App\Model\News\Comments\Comment;
use App\Model\User\User;
use System\App\Model\AbstractFactoryModel;

class VotingFactory extends AbstractFactoryModel
{

    protected function getTable(): string
    {
        return "cms_news_comments_votes";
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
     * @param Comment $comment
     * @return Voting[]
     * @throws \Exception
     */
    public function getAllByComment(Comment $comment): array
    {
        $query = $this->getConnection()->getPDO()->prepare('SELECT `id` FROM `' . $this->getTable() . '` WHERE `comment_id` = :comment_id');
        $query->execute([':comment_id' => $comment->getInt("id")]);

        $data = [];

        if ($query->rowCount() > 0) {
            while ($row = $query->fetchObject()) {
                $data[] = $this->getByRow($row);
            }
        }

        return $data;
    }

	/**
	 * @param Comment $comment
	 *
	 * @return int
	 */
    public function getDislikeCountByComment(Comment $comment): int
    {
        $query = $this->getConnection()->getPDO()->prepare('SELECT COUNT(`id`) as `dislikes` FROM `' . $this->getTable() . '` WHERE `type` = :type AND `comment_id` = :comment_id');
        $query->execute([':comment_id' => $comment->getInt("id"), ':type' => 0]);

        $result = $query->fetchObject();

        return (int)$result->dislikes;
    }

	/**
	 * @param Comment $comment
	 *
	 * @return int
	 */
    public function getLikeCountByComment(Comment $comment): int
    {
        $query = $this->getConnection()->getPDO()->prepare('SELECT COUNT(`id`) as `likes` FROM `' . $this->getTable() . '` WHERE `type` = :type AND `comment_id` = :comment_id');
        $query->execute([':comment_id' => $comment->getInt("id"), ':type' => 1]);

        $result = $query->fetchObject();

        return (int)$result->likes;
    }

    /**
     * @param Comment $comment
     * @param User $user
     * @return Voting|null
     * @throws \Exception
     */
    public function getByCommentAndUser(Comment $comment, User $user): ?Voting
    {
        $query = $this->getConnection()->getPDO()->prepare('SELECT `id` FROM `' . $this->getTable() . '` WHERE `comment_id` = :comment_id AND `user_id` = :user_id');
        $query->execute([':comment_id' => $comment->getInt("id"), ':user_id' => $user->getInt("id")]);

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