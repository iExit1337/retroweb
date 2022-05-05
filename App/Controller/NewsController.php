<?php


namespace App\Controller;


use App\Model\News\Comments\Comment;
use App\Model\News\Comments\CommentsFactory;
use App\Model\News\Comments\Voting\VotingFactory;
use App\Model\News\News;
use App\Model\News\NewsFactory;
use System\App\View\Template;
use System\Helpers\JSONWriter;
use System\HTTP\IRoutable;
use System\HTTP\Request\Request;
use System\HTTP\Request\RequestType;
use System\HTTP\Route;
use System\Navigation\Point;
use System\Security\CSRF;

class NewsController extends WebsiteController implements IRoutable
{

    /**
     * @var $_communityTab Point
     */
    private $_communityTab;
    /**
     * @var $_newsTab Point
     */
    private $_newsTab;

    public function onRegistration(): void
    {
        $this->_communityTab = $this->getNavigation()->getById("Community");
        $this->_newsTab = new Point("Community.News", "News", $this->getApp()->getConfig()->get("site", "url") . "articles", 9998);

        $this->_communityTab->add($this->_newsTab);
    }

    /**
     * Returns the routes of the Controller
     * @return array
     */
    public function getRoutes(): array
    {
        return [
            new Route(RequestType::GET, '/articles', 'latest-article'),
            new Route(RequestType::GET, '/articles/{id:\d+}', 'by-id'),

            new Route(RequestType::GET, '/articles/{id:\d+}/vote/{type}/{csrf_token}', 'vote-news'),
            new Route(RequestType::POST, '/articles/comment/add', 'add-comment'),

            new Route(RequestType::GET, '/articles/{id:\d+}/delete/{comment_id}/{csrf_token}', 'delete-comment'),

            new Route(RequestType::GET, '/articles/comment/vote/{news_id:\d+}/{comment_id:\d+}/{type}/{csrf_token}', 'vote-comment')
        ];
    }

    /**
     * @param Request $request
     * @param Route $route
     * @param array $vars
     * @throws \Exception
     */
    public function onRequest(Request $request, Route $route, array $vars): void
    {
        $this->_communityTab->setActive(true);
        $this->_newsTab->setActive(true);

        $sessionUser = $this->getSession()->getUser();

        /**
         * @var $newsFactory NewsFactory
         */
        $newsFactory = $this->getFactoryManager()->get(NewsFactory::class);

        if ($route->getHandler() == 'latest-article') {
            $news = $newsFactory->getLatestByLimit(1)[0];
        } else if ($route->getHandler() == 'by-id' || $route->getHandler() == 'vote-news') {
            $news = $newsFactory->getById($vars['id']);
            if ($news == null) {
                $this->redirect('articles');
            }
        }

        if ($route->getHandler() == 'vote-news') {
            $type = base64_decode($vars['type']);
            $csrfToken = $vars['csrf_token'];

            if ($this->minRank(1) && CSRF::isValid($csrfToken) && ($type == 1 || $type == 0) && $news->isVotingEnabled()) {
                if ($news->getVoteByUser($sessionUser) == null) {
                    $news->addVote($type, $sessionUser);
                }
            }

            $this->redirect('articles/' . $news->getInt("id"));
            return;
        }

        if ($route->getHandler() == 'vote-comment') {
            $newsId = $vars['news_id'];
            $commentId = $vars['comment_id'];
            $type = base64_decode($vars['type']);
            $csrfToken = $vars['csrf_token'];

            /**
             * @var $commentsFactory CommentsFactory
             */
            $commentsFactory = $this->getFactoryManager()->get(CommentsFactory::class);
            /**
             * @var $news News
             */
            $news = $newsFactory->getById($newsId);
            /**
             * @var $comment Comment
             */
            $comment = $commentsFactory->getById($commentId);
            if (CSRF::isValid($csrfToken) && $news != null && $sessionUser != null && $comment != null) {
                if ($type == 1 || $type == 0) {
                    $userVote = $comment->getVoteByUser($sessionUser);
                    if ($userVote == null) {
                        $commentsVoteFactory = $this->getFactoryManager()->get(VotingFactory::class);
                        $commentsVoteFactory->createObject([
                            'comment_id' => $comment->getInt("id"),
                            'user_id' => $sessionUser->getInt("id"),
                            'type' => $type
                        ]);
                    }
                }
            }

            $this->redirect('articles/' . $news->getInt("id"));
            return;
        }

        if ($route->getHandler() == 'delete-comment') {
            $newsId = $vars['id'];
            $news = $newsFactory->getById($newsId);
            if ($news != null && $sessionUser != null && $sessionUser->canDeleteComments() && CSRF::isValid($vars['csrf_token'])) {
                $commentsFactory = $this->getFactoryManager()->get(CommentsFactory::class);
                //echo base64_decode($vars['comment_id']);
                $comment = $commentsFactory->getById(base64_decode($vars['comment_id']));
                if ($comment != null) {
                    $comment->delete();
                }
            }

            $this->redirect('articles/' . $newsId);

            return;
        }

        if ($route->getHandler() == 'add-comment' && $request->getMethod() == RequestType::POST) {
            $json = new JSONWriter();
            $json->write('error', true);
            $json->write('success', false);
            $json->write('error_msg', "Ein uns unbekannter Fehler ist aufgetreten. Bitte versuch es erneut.");

            if (CSRF::isValid($request->getPost("token"), false)) {
                if ($sessionUser != null) {
                    /**
                     * @var $news News|null
                     */
                    $news = $newsFactory->getById($request->getPost("news_id"));
                    if ($news != null) {
                        if ($sessionUser->getInt("can_comment") == 1) {
                            if ($news->isCommentingEnabled()) {

                                $config = $this->getApp()->getConfig();

                                $text = $request->getPost("comment");
                                $minLength = $config->getInt("news_comments", "min_length");
                                $maxLength = $config->getInt("news_comments", "max_length");

                                if (strlen($text) >= $minLength && strlen($text) <= $maxLength) {
                                    $minWords = $config->getInt("news_comments", "min_words");
                                    if (explode(" ", trim($text)) >= $minWords) {
                                        $latestComment = $news->getLatestComment();
                                        $timeout = $config->getInt("news_comments", "comment_timeout");
                                        if ($latestComment == null || $latestComment->getAuthor()->getInt("id") != $sessionUser->getInt("id") || ($latestComment->getAuthor()->getInt("id") == $sessionUser->getInt("id") && $latestComment->get("timestamp") + $timeout < time())) {
                                            $news->addComment($text, $sessionUser);
                                            $json->write('error_msg', null);
                                            $json->write('error', false);
                                            $json->write('success', true);
                                        } else {
                                            $json->write('error_msg', 'Du kannst diese News nur alle 3 Minuten kommentieren!');
                                        }
                                    } else {
                                        $json->write('error_msg', 'Der Kommentar muss mindestens ' . $minWords . ' ' . ($minWords > 1 ? 'W&ouml;rtern' : 'Wort') . ' enthalten.');
                                    }
                                } else {
                                    $json->write('error_msg', 'Der Kommentar darf nur zwischen ' . $minLength . ' und ' . $maxLength . ' Zeichen lang sein');
                                }
                            } else {
                                $json->write('error_msg', 'Du kannst diese News nicht kommentieren.');
                            }
                        } else {
                            $json->write('error_msg', 'Du kannst keine News kommentieren.');
                        }
                    } else {
                        $json->write('error_msg', 'Dieser Artikel existiert nicht mehr.');
                    }
                } else {
                    $json->write('error_msg', 'Du musst eingeloggt sein um Kommentieren zu können!');
                }
            }
            echo $json;

            return;
        }

        $this->addCSSFile("news/News");
        $this->setPageTitle($news->get("title") . " - News");

        /**
         * @var $template Template
         */
        $template = $this->getView()->createTemplate("news/News.tpl.php");
        $template->username = $sessionUser != null ? $sessionUser->get("username") : "Gast";
        $template->news = $news;
        $template->newsList = $newsFactory->getLatestByLimit(10);

        $isVotingEnabled = $news->isVotingEnabled();
        $template->isVotingEnabled = $isVotingEnabled;

        if ($isVotingEnabled) {
            if ($sessionUser != null) {
                $myVote = $news->getVoteByUser($sessionUser);
                $template->myVote = $myVote;
            } else {
                $template->myVote = null;
            }

            $template->canUserVote = $news->isVotingEnabled() && $sessionUser != null && $myVote == null;
            $template->likes = $news->getLikesCount();
            $template->dislikes = $news->getDislikesCount();
        }

        $isCommentingEnabled = $news->isCommentingEnabled();
        if ($isCommentingEnabled) {
            $canUserComment = $sessionUser != null && $sessionUser->getInt("can_comment") == 1;

            $template->canUserComment = $canUserComment;

            if ($canUserComment) {
                $this->addJSFile("news/News");
            }
        }

        $template->isCommentingEnabled = $isCommentingEnabled;
        $template->comments = $news->getComments();
        $this->display($template);
    }
}